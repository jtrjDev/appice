<?php

namespace App\Services;

use App\Models\Tenant\Configuracao;
use App\Models\Tenant\Pedido;
use App\Models\Tenant\Produto;
use App\Models\Tenant\Cliente;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FocusNFeService
{
    protected $config;
    protected $token;
    protected $isSandbox;
    protected $baseUrl;

    public function __construct(Configuracao $config)
    {
        $this->config = $config;
        $this->token = $config->focus_token;
        $this->isSandbox = ($config->ambiente_nf === 'homologacao');
        $this->baseUrl = $this->isSandbox 
            ? 'https://homologacao.focusnfe.com.br/v2'
            : 'https://api.focusnfe.com.br/v2';
    }

    /**
     * Emite NFC-e (para consumidor final)
     */
    public function emitirNFCe(Pedido $pedido, $cliente)
{
    $referencia = 'PED_' . $pedido->id;
    
    $data = $this->montarDadosNFCe($pedido, $cliente);
    
    // Log do JSON enviado
    \Log::info('JSON enviado para Focus:', ['json' => json_encode($data, JSON_PRETTY_PRINT)]);
    
    $response = Http::withHeaders([
        'Authorization' => 'Basic ' . base64_encode($this->token . ':'),
        'Content-Type' => 'application/json',
    ])->timeout(60)->post($this->baseUrl . "/nfce?ref=" . $referencia, $data);
    
    \Log::info('Resposta Focus:', [
        'status' => $response->status(),
        'body' => $response->body()
    ]);
    
    return [
        'success' => $response->successful() && isset($resultado['status']) && $resultado['status'] === 'autorizado',
        'data' => $response->json(),
        'status_code' => $response->status()
    ];
}
    
    /**
     * Emite NF-e (para cliente com CNPJ)
     */
    public function emitirNFe(Pedido $pedido, $cliente)
    {
        $referencia = 'PED_' . $pedido->id;
        
        $data = $this->montarDadosNFe($pedido, $cliente);
        
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->token . ':'),
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . "/nfe?ref=" . $referencia, $data);
        
        $resultado = $response->json();
        
        return [
            'success' => $response->successful() && isset($resultado['status']) && $resultado['status'] === 'autorizado',
            'data' => $resultado,
            'status_code' => $response->status()
        ];
    }
    
    /**
     * Consultar nota fiscal
     */
    public function consultar($referencia)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->token . ':'),
        ])->get($this->baseUrl . "/nfce/" . $referencia);
        
        return $response->json();
    }
    protected function getAuthHeader()
{
    return 'Basic ' . base64_encode($this->token . ':');
}
    /**
     * Cancelar nota fiscal
     */
    public function cancelar($referencia, $justificativa)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->token . ':'),
            'Content-Type' => 'application/json',
        ])->delete($this->baseUrl . "/nfce/" . $referencia, [
            'justificativa' => $justificativa
        ]);
        
        return $response->json();
    }
    
    /**
     * Baixar DANFE (PDF)
     */
    public function baixarDanfe($referencia)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->token . ':'),
        ])->get($this->baseUrl . "/nfce/" . $referencia . "/pdf");
        
        return $response->body();
    }
    
    /**
     * Monta os dados da NFC-e (consumidor final)
     */
    private function montarDadosNFCe(Pedido $pedido, $cliente)
{
    $itens = [];
    foreach ($pedido->itens as $key => $item) {
        $produto = Produto::find($item->produto_id);
        $itens[] = [
            "numero_item" => $key + 1,
            "codigo_ncm" => $produto->ncm ?? "00000000",
            "quantidade_comercial" => (float) $item->quantidade,
            "quantidade_tributavel" => (float) $item->quantidade,
            "cfop" => "5102",
            "valor_unitario_comercial" => (float) $item->preco_unitario,
            "valor_unitario_tributavel" => (float) $item->preco_unitario,
            "descricao" => $item->produto_nome,
            "codigo_produto" => $produto->codigo ?? $item->produto_id,
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
        "cnpj_emitente" => preg_replace('/\D/', '', $this->config->cpf_cnpj),
        "data_emissao" => now()->format("Y-m-d\TH:i:sP"),
        "natureza_operacao" => "VENDA AO CONSUMIDOR",
        "presenca_comprador" => "1",
        "modalidade_frete" => "9",
        "itens" => $itens,
        "formas_pagamento" => $formasPagamento
    ];
    
    // Adiciona cliente se tiver CPF
    if ($cliente && $cliente->cpf_cnpj) {
        $cpf = preg_replace('/\D/', '', $cliente->cpf_cnpj);
        if (strlen($cpf) == 11) {
            $dados["cpf_destinatario"] = $cpf;
            $dados["nome_destinatario"] = $cliente->nome;
        }
    }
    
    return $dados;
}
    
    private function getFormaPagamentoFocus($forma)
    {
        $map = [
            'dinheiro' => '01',
            'cartao_credito' => '03',
            'cartao_debito' => '04',
            'pix' => '17',
        ];
        return $map[$forma] ?? '01';
    }
}