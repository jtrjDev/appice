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

    public function __construct(int $pedidoId, string $tenantId)
    {
        $this->pedidoId = $pedidoId;
        $this->tenantId = $tenantId;
    }

    public function handle()
    {
        try {
            Log::info('=== INICIANDO EMISSÃO DE NOTA ===');
            
            $tenant = \App\Models\Tenant::find($this->tenantId);
            if (!$tenant) {
                Log::error("Tenant não encontrado: {$this->tenantId}");
                return;
            }

            Tenancy::initialize($tenant);
            Log::info('Tenancy inicializado para: ' . $tenant->id);

            $pedido = Pedido::with(['itens', 'cliente'])->find($this->pedidoId);
            $config = Configuracao::first();

            if (!$config || !$config->focus_token) {
                throw new \Exception('Token Focus não configurado');
            }

            $isSandbox = ($config->ambiente_nf === 'homologacao');
            $baseUrl = $isSandbox 
                ? 'https://homologacao.focusnfe.com.br'
                : 'https://api.focusnfe.com.br';

            // Monta os dados da NFC-e
            $data = $this->montarDadosNFCe($pedido, $config);

            Log::info('Dados enviados para Focus:', ['data' => $data]);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($config->focus_token . ':'),
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($baseUrl . "/v2/nfce?ref=PED_" . $pedido->id, $data);

            $resultado = $response->json();
            Log::info('Resposta Focus:', ['status' => $response->status(), 'body' => $resultado]);

            $success = $response->successful() && isset($resultado['status']) && $resultado['status'] === 'autorizado';

            // Monta URLs completas
            $linkPdf = null;
            $linkXml = null;
            
            if ($success && isset($resultado['caminho_danfe'])) {
                $linkPdf = $baseUrl . $resultado['caminho_danfe'];
                $linkXml = $baseUrl . $resultado['caminho_xml_nota_fiscal'];
            }

            $nota = NotaFiscal::create([
                'pedido_id' => $pedido->id,
                'modelo' => 'nfce',
                'referencia' => 'PED_' . $pedido->id,
                'numero_nota' => $resultado['numero'] ?? null,
                'chave_acesso' => $resultado['chave_nfe'] ?? null,
                'link_xml' => $linkXml,
                'link_pdf' => $linkPdf,
                'status' => $success ? 'autorizada' : 'erro',
                'mensagem_erro' => $success ? null : ($resultado['mensagem'] ?? 'Erro na comunicação'),
                'retorno_completo' => $resultado,
            ]);

            Log::info("Nota fiscal criada: ID {$nota->id} para pedido {$pedido->id}");

        } catch (\Exception $e) {
            Log::error("Erro ao emitir nota: " . $e->getMessage());
            
            NotaFiscal::create([
                'pedido_id' => $this->pedidoId,
                'modelo' => 'nfce',
                'referencia' => 'PED_' . $this->pedidoId,
                'status' => 'erro',
                'mensagem_erro' => $e->getMessage(),
            ]);
        }
    }

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
        
        foreach ($pedido->pagamentos as $pagamento) {
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
}