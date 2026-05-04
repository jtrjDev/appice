<?php

namespace App\Jobs;

use App\Models\Tenant\NotaFiscal;
use App\Models\Tenant\Pedido;
use App\Models\Tenant\Configuracao;
use App\Models\Tenant\Produto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Facades\Tenancy;

class EmitirNotaFiscal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $pedidoId;
    protected $tenantId;
    protected $tipoDocumento;

    public function __construct(int $pedidoId, $tenantId, string $tipoDocumento = 'CPF')
    {
        $this->pedidoId = $pedidoId;
        $this->tenantId = $tenantId;
        $this->tipoDocumento = $tipoDocumento;
    }

   public $timeout = 180;

public function handle()
{
    $modelo = 'nfce';
    $referencia = 'PED_' . $this->pedidoId;

    try {
        Log::info('=== INICIANDO EMISSÃO DE NOTA ===');
        Log::info('Tipo de documento: ' . ($this->tipoDocumento ?? 'não definido'));
        Log::info('Tenant ID: ' . $this->tenantId);

        $tenant = \App\Models\Tenant::find($this->tenantId);

        if (!$tenant) {
            Log::error("❌ Tenant não encontrado: {$this->tenantId}");
            return;
        }

        tenancy()->initialize($tenant);

        Log::info('Tenancy inicializado para: ' . $tenant->id);

        $pedido = Pedido::with(['itens', 'cliente'])->find($this->pedidoId);

        if (!$pedido) {
            Log::error("❌ Pedido não encontrado: {$this->pedidoId}");
            return;
        }

        $config = Configuracao::first();

        if (!$config || !$config->focus_token) {
            throw new \Exception('Token Focus não configurado');
        }

        $isSandbox = ($config->ambiente_nf === 'homologacao');

        $baseUrl = $isSandbox
            ? 'https://homologacao.focusnfe.com.br'
            : 'https://api.focusnfe.com.br';

        $isCnpj = ($this->tipoDocumento ?? 'CPF') === 'CNPJ';

        $modelo = $isCnpj ? 'nfe' : 'nfce';

        $prefixo = $isCnpj ? 'NFE_' : 'NFC_';
        $referencia = $prefixo . $pedido->id;

        Log::info("Emitindo nota modelo: {$modelo} com referência: {$referencia}");

        if ($isCnpj) {
            $data = $this->montarDadosNFe($pedido, $config);
            $endpoint = '/v2/nfe';
        } else {
            $data = $this->montarDadosNFCe($pedido, $config);
            $endpoint = '/v2/nfce';
        }

        Log::info('Dados enviados para Focus:', ['data' => $data]);

        /*
        |--------------------------------------------------------------------------
        | Cria ou atualiza a nota antes do envio
        |--------------------------------------------------------------------------
        */
        $nota = NotaFiscal::updateOrCreate(
            [
                'referencia' => $referencia,
            ],
            [
                'pedido_id' => $pedido->id,
                'modelo' => $modelo,
                'status' => 'processando',
                'mensagem_erro' => null,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Envia para Focus
        |--------------------------------------------------------------------------
        */
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($config->focus_token . ':'),
            'Content-Type' => 'application/json',
        ])->timeout(60)->post($baseUrl . $endpoint . '?ref=' . urlencode($referencia), $data);

        $resultado = $response->json() ?? [];

        Log::info('Resposta inicial Focus:', [
            'status_http' => $response->status(),
            'body' => $resultado,
        ]);

        if (!$response->successful()) {
            $nota->update([
                'status' => 'erro',
                'mensagem_erro' => $resultado['mensagem'] ?? 'Erro na comunicação com a Focus',
                'retorno_completo' => $resultado,
            ]);

            Log::error('❌ Erro inicial ao enviar nota para Focus', [
                'status_http' => $response->status(),
                'body' => $resultado,
            ]);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | NF-e pode demorar. Consulta algumas vezes até autorizar.
        |--------------------------------------------------------------------------
        */
        if ($isCnpj) {
            $resultado = $this->aguardarAutorizacaoNFe(
                baseUrl: $baseUrl,
                token: $config->focus_token,
                referencia: $referencia,
                resultadoInicial: $resultado
            );
        }

        /*
        |--------------------------------------------------------------------------
        | NFC-e normalmente já vem autorizada no primeiro retorno
        |--------------------------------------------------------------------------
        */
        $statusFocus = $resultado['status'] ?? null;

        $success = $statusFocus === 'autorizado';

        $linkPdf = null;
        $linkXml = null;

        if ($success) {
            if (!empty($resultado['caminho_danfe'])) {
                $linkPdf = $baseUrl . $resultado['caminho_danfe'];
            }

            if (!empty($resultado['caminho_xml_nota_fiscal'])) {
                $linkXml = $baseUrl . $resultado['caminho_xml_nota_fiscal'];
            }
        }

        $statusSistema = match ($statusFocus) {
            'autorizado' => 'autorizada',
            'processando_autorizacao' => 'processando',
            'erro_autorizacao' => 'erro',
            default => $success ? 'autorizada' : 'erro',
        };

        $nota->update([
            'numero_nota' => $resultado['numero'] ?? null,
            'chave_acesso' => $resultado['chave_nfe'] ?? null,
            'link_xml' => $linkXml,
            'link_pdf' => $linkPdf,
            'status' => $statusSistema,
            'mensagem_erro' => $success ? null : ($resultado['mensagem'] ?? 'Nota ainda não autorizada'),
            'retorno_completo' => $resultado,
        ]);

        Log::info("✅ Nota fiscal atualizada: ID {$nota->id}", [
            'status_focus' => $statusFocus,
            'status_sistema' => $statusSistema,
            'chave' => $resultado['chave_nfe'] ?? null,
            'pdf' => $linkPdf,
            'xml' => $linkXml,
        ]);

    } catch (\Exception $e) {
        Log::error("❌ Erro ao emitir nota: " . $e->getMessage());

        NotaFiscal::updateOrCreate(
            [
                'referencia' => $referencia,
            ],
            [
                'pedido_id' => $this->pedidoId,
                'modelo' => $modelo,
                'status' => 'erro',
                'mensagem_erro' => $e->getMessage(),
            ]
        );
    }
}


private function aguardarAutorizacaoNFe(string $baseUrl, string $token, string $referencia, array $resultadoInicial): array
{
    $resultado = $resultadoInicial;

    $status = $resultado['status'] ?? null;

    if ($status === 'autorizado' || $status === 'erro_autorizacao') {
        return $resultado;
    }

    /*
    |--------------------------------------------------------------------------
    | Tenta consultar a NF-e algumas vezes
    | 12 tentativas x 5 segundos = até 60 segundos aguardando autorização
    |--------------------------------------------------------------------------
    */
    $maxTentativas = 12;
    $segundosEntreTentativas = 5;

    for ($tentativa = 1; $tentativa <= $maxTentativas; $tentativa++) {
        sleep($segundosEntreTentativas);

        Log::info("Consultando NF-e na Focus. Tentativa {$tentativa}/{$maxTentativas}", [
            'referencia' => $referencia,
        ]);

        $consulta = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($token . ':'),
            'Accept' => 'application/json',
        ])->timeout(30)->get($baseUrl . '/v2/nfe/' . urlencode($referencia), [
            'completa' => 1,
        ]);

        $bodyConsulta = $consulta->json() ?? [];

        Log::info('Resposta consulta NF-e Focus:', [
            'status_http' => $consulta->status(),
            'body' => $bodyConsulta,
        ]);

        if (!$consulta->successful()) {
            $resultado = $bodyConsulta;
            continue;
        }

        $resultado = $bodyConsulta;
        $status = $resultado['status'] ?? null;

        if ($status === 'autorizado') {
            Log::info('✅ NF-e autorizada após consulta.', [
                'referencia' => $referencia,
                'tentativa' => $tentativa,
            ]);

            return $resultado;
        }

        if ($status === 'erro_autorizacao') {
            Log::warning('❌ NF-e retornou erro de autorização.', [
                'referencia' => $referencia,
                'tentativa' => $tentativa,
                'mensagem' => $resultado['mensagem'] ?? null,
            ]);

            return $resultado;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Se chegou aqui, ainda está processando
    |--------------------------------------------------------------------------
    */
    Log::warning('⚠️ NF-e ainda em processamento após todas as tentativas.', [
        'referencia' => $referencia,
        'ultimo_resultado' => $resultado,
    ]);

    return $resultado;
}
    /**
     * Monta dados para NFCe (CPF)
     */
    private function montarDadosNFCe(Pedido $pedido, $config)
    {
        $itens = [];

        foreach ($pedido->itens as $key => $item) {
            $itens[] = [
                "numero_item" => $key + 1,
                "codigo_ncm" => "00000000",
                "quantidade_comercial" => (float) $item->quantidade,
                "quantidade_tributavel" => (float) $item->quantidade,
                "cfop" => "5102",
                "valor_unitario_comercial" => (float) $item->preco_unitario,
                "valor_unitario_tributavel" => (float) $item->preco_unitario,
                "descricao" => $item->produto_nome,
                "codigo_produto" => $item->produto_id,
                "unidade_comercial" => "UN",
                "unidade_tributavel" => "UN",
                "icms_origem" => "0",
                "icms_situacao_tributaria" => "102"
            ];
        }

        $formasPagamento = [];
        $tipoMap = [
            'dinheiro' => '01',
            'cartao_credito' => '03',
            'cartao_debito' => '04',
            'pix' => '17'
        ];

        foreach ($pedido->pagamentos ?? [] as $pagamento) {
            $forma = $tipoMap[$pagamento['forma']] ?? '01';
            $formasPagamento[] = [
                "forma_pagamento" => $forma,
                "valor_pagamento" => (float) $pagamento['valor']
            ];
        }

        $dados = [
            "cnpj_emitente" => preg_replace('/\D/', '', $config->cpf_cnpj),
            "data_emissao" => now()->format("Y-m-d\TH:i:sP"),
            "natureza_operacao" => "VENDA AO CONSUMIDOR",
            "presenca_comprador" => "1",
            "modalidade_frete" => "9",
            "itens" => $itens,
            "formas_pagamento" => $formasPagamento
        ];

        // Adiciona cliente se tiver CPF
        if ($pedido->cliente && $pedido->cliente->cpf_cnpj) {
            $cpf = preg_replace('/\D/', '', $pedido->cliente->cpf_cnpj);
            if (strlen($cpf) == 11) {
                $dados["cpf_destinatario"] = $cpf;
                $dados["nome_destinatario"] = $pedido->cliente->nome;
            }
        }

        return $dados;
    }

    /**
     * Monta dados para NFe (CNPJ)
     */
    private function montarDadosNFe(Pedido $pedido, $config)
    {
        $cnpjEmitente = preg_replace('/\D/', '', (string) $config->cpf_cnpj);

        if (strlen($cnpjEmitente) !== 14) {
            throw new \Exception('CNPJ do emitente inválido na configuração.');
        }

        $cliente = $pedido->cliente;
        $itens = [];

        foreach ($pedido->itens as $key => $item) {
            $produto = Produto::find($item->produto_id);

            $itens[] = [
                "numero_item" => (string) ($key + 1),
                "codigo_produto" => (string) ($produto->codigo ?? $item->produto_id),
                "descricao" => substr($item->produto_nome, 0, 120),
                "cfop" => (string) ($produto->cfop ?? "5102"),
                "unidade_comercial" => $produto->unidade ?? "UN",
                "quantidade_comercial" => number_format((float) $item->quantidade, 2, ".", ""),
                "valor_unitario_comercial" => number_format((float) $item->preco_unitario, 2, ".", ""),
                "valor_unitario_tributavel" => number_format((float) $item->preco_unitario, 2, ".", ""),
                "unidade_tributavel" => $produto->unidade ?? "UN",
                "codigo_ncm" => $produto->ncm ?? "00000000",
                "quantidade_tributavel" => number_format((float) $item->quantidade, 2, ".", ""),
                "valor_bruto" => number_format((float) $item->subtotal, 2, ".", ""),
                "icms_situacao_tributaria" => (string) ($produto->cst_icms ?? "102"),
                "icms_origem" => (string) ($produto->origem ?? "0"),
                "pis_situacao_tributaria" => (string) ($produto->cst_pis ?? "07"),
                "cofins_situacao_tributaria" => (string) ($produto->cst_cofins ?? "07")
            ];
        }

        $dados = [
            "natureza_operacao" => "Remessa",
            "data_emissao" => now()->format("Y-m-d\TH:i:sP"),
            "data_entrada_saida" => now()->format("Y-m-d\TH:i:sP"),
            "tipo_documento" => "1",
            "finalidade_emissao" => "1",

            "cnpj_emitente" => $cnpjEmitente,
            "nome_emitente" => $config->razao_social,
            "nome_fantasia_emitente" => $config->nome_fantasia,
            "logradouro_emitente" => $config->endereco,
            "numero_emitente" => (string) $config->numero,
            "bairro_emitente" => $config->bairro,
            "municipio_emitente" => $config->cidade,
            "uf_emitente" => $config->uf,
            "cep_emitente" => preg_replace('/\D/', '', (string) $config->cep),
            "inscricao_estadual_emitente" => $config->inscricao_estadual,

            "valor_frete" => "0.00",
            "valor_seguro" => "0.00",
            "valor_total" => number_format((float) $pedido->total, 2, ".", ""),
            "valor_produtos" => number_format((float) $pedido->subtotal, 2, ".", ""),
            "modalidade_frete" => "0",

            "items" => $itens
        ];

        if ($cliente && $cliente->cpf_cnpj) {
            $cpfCnpj = preg_replace('/\D/', '', $cliente->cpf_cnpj);

            if (strlen($cpfCnpj) === 14) {
                $dados["cnpj_destinatario"] = $cpfCnpj;
            } else {
                $dados["cpf_destinatario"] = $cpfCnpj;
            }

            $dados["nome_destinatario"] = $cliente->nome;
            $dados["inscricao_estadual_destinatario"] = $cliente->inscricao_estadual ?? null;
            $telefone = preg_replace('/\D/', '', $cliente->telefone ?? '');

            if (strlen($telefone) < 10) {
                throw new \Exception('Telefone do destinatário inválido para emissão da NF-e.');
            }

            $cep = preg_replace('/\D/', '', $cliente->cep ?? '');

            if (strlen($cep) !== 8) {
                throw new \Exception('CEP do destinatário inválido para emissão da NF-e.');
            }

            if (empty($cliente->cidade)) {
                throw new \Exception('Município do destinatário obrigatório para emissão da NF-e.');
            }

            if (empty($cliente->uf)) {
                throw new \Exception('UF do destinatário obrigatória para emissão da NF-e.');
            }

            $dados["telefone_destinatario"] = $telefone;
            $dados["logradouro_destinatario"] = $cliente->endereco;
            $dados["numero_destinatario"] = (string) $cliente->numero;
            $dados["bairro_destinatario"] = $cliente->bairro;
            $dados["municipio_destinatario"] = $cliente->cidade;
            $dados["uf_destinatario"] = $cliente->uf;
            $dados["pais_destinatario"] = "Brasil";
            $dados["cep_destinatario"] = $cep;
        }

        return $dados;
    }
}
