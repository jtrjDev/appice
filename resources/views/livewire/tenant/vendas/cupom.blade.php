<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cupom #{{ $pedido->numero_pedido }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 280px;
            margin: 0 auto;
            padding: 12px 10px;
            font-size: 11px;
            line-height: 1.4;
            background: white;
        }
        
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
            button {
                display: none;
            }
        }
        
        /* Utilitários */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-uppercase { text-transform: uppercase; }
        .bold { font-weight: bold; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mt-1 { margin-top: 4px; }
        .mt-2 { margin-top: 8px; }
        .pt-1 { padding-top: 4px; }
        
        /* Linhas */
        .line-dashed {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .line-dotted {
            border-top: 1px dotted #000;
            margin: 6px 0;
        }
        .line-solid {
            border-top: 2px solid #000;
            margin: 6px 0;
        }
        
        /* Layout de linha */
        .row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        
        .row-qtd {
            display: flex;
            gap: 6px;
        }
        
        .qtd {
            min-width: 50px;
        }
        
        .desc {
            flex: 1;
        }
        
        .total {
            font-weight: bold;
        }
        
        /* Tabela de itens */
        .item-header {
            font-weight: bold;
            border-bottom: 1px dotted #000;
            padding-bottom: 2px;
            margin-bottom: 4px;
        }
        
        .item-row {
            margin: 5px 0;
        }
        
        .item-nome {
            font-size: 11px;
            margin-bottom: 2px;
        }
        
        .item-detalhes {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #444;
        }
        
        /* Informações da empresa */
        .empresa-nome {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .empresa-dados {
            font-size: 9px;
            color: #444;
        }
        
        /* QR Code para PIX */
        .qr-code {
            text-align: center;
            margin: 10px 0;
        }
        
        .qr-code img {
            width: 100px;
            height: 100px;
            margin: 0 auto;
        }
        
        /* Rodapé */
        .footer {
            font-size: 9px;
            text-align: center;
            color: #666;
            margin-top: 10px;
        }
        
        /* Botão imprimir */
        .btn-print {
            display: block;
            width: 100%;
            padding: 8px;
            margin-top: 15px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-family: monospace;
            font-size: 11px;
            cursor: pointer;
        }
        
        .btn-print:hover {
            background: #333;
        }
        
        /* Resumo de pagamentos */
        .pagamento-item {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        
        .troco {
            color: #0066cc;
            font-size: 10px;
        }
    </style>
</head>
<body>

    {{-- CABEÇALHO DA EMPRESA --}}
    <div class="text-center mb-2">
        <div class="empresa-nome">{{ $configuracao->nome_fantasia ?? $configuracao->razao_social ?? 'SEU ESTABELECIMENTO' }}</div>
        <div class="empresa-dados">
            @if($configuracao->cpf_cnpj ?? false)
                {{ $configuracao->cpf_cnpj_formatado ?? $configuracao->cpf_cnpj }}<br>
            @endif
            @if($configuracao->endereco ?? false)
                {{ $configuracao->endereco }}, {{ $configuracao->numero }}<br>
                {{ $configuracao->bairro }} - {{ $configuracao->cidade }}/{{ $configuracao->estado }}
            @endif
        </div>
    </div>

    <div class="line-solid"></div>

    {{-- INFORMAÇÕES DO PEDIDO --}}
    <div class="mb-2">
        <div class="row">
            <span>PEDIDO:</span>
            <span class="bold">#{{ $pedido->numero_pedido }}</span>
        </div>
        <div class="row">
            <span>DATA:</span>
            <span>{{ $pedido->created_at->format('d/m/Y H:i:s') }}</span>
        </div>
        @if($pedido->tipo == 'mesa' && $pedido->mesa)
        <div class="row">
            <span>MESA/COMANDA:</span>
            <span class="bold">{{ $pedido->mesa }}</span>
        </div>
        @endif
        <div class="row">
            <span>ATENDENTE:</span>
            <span>{{ $pedido->atendente->name ?? $pedido->atendente_id }}</span>
        </div>
        <div class="row">
            <span>CLIENTE:</span>
            <span>{{ $pedido->cliente->nome ?? 'CONSUMIDOR' }}</span>
        </div>
        @if($pedido->cliente && $pedido->cliente->cpf_cnpj)
        <div class="row">
            <span>CPF/CNPJ:</span>
            <span>{{ $pedido->cliente->cpf_cnpj_formatado ?? $pedido->cliente->cpf_cnpj }}</span>
        </div>
        @endif
    </div>

    <div class="line-dashed"></div>

    {{-- ITENS DO PEDIDO --}}
    <div class="mb-2">
        <div class="row bold item-header">
            <span>ITEM</span>
            <span>QTD</span>
            <span>VL.UNIT</span>
            <span>TOTAL</span>
        </div>
        
        @foreach($pedido->itens as $item)
        <div class="item-row">
            <div class="item-nome">{{ $item->produto_nome }}</div>
            <div class="item-detalhes">
                <span>
                    {{ number_format($item->quantidade, $item->quantidade == intval($item->quantidade) ? 0 : 3, ',', '.') }} x
                </span>
                <span>R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</span>
                <span class="bold">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</span>
            </div>
            
            {{-- Observações do item (se tiver) --}}
            @if($item->observacao)
            <div class="text-left" style="font-size: 9px; color: #666; margin-top: 2px;">
                Obs: {{ $item->observacao }}
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <div class="line-dashed"></div>

    {{-- RESUMO DO PEDIDO --}}
    <div class="mb-2">
        <div class="row">
            <span>SUBTOTAL</span>
            <span>R$ {{ number_format($pedido->subtotal, 2, ',', '.') }}</span>
        </div>
        
        @if($pedido->taxa_entrega > 0)
        <div class="row">
            <span>TAXA ENTREGA</span>
            <span>R$ {{ number_format($pedido->taxa_entrega, 2, ',', '.') }}</span>
        </div>
        @endif
        
        @if($pedido->desconto > 0)
        <div class="row">
            <span>DESCONTO</span>
            <span>- R$ {{ number_format($pedido->desconto, 2, ',', '.') }}</span>
        </div>
        @endif
        
        <div class="line-dotted"></div>
        
        <div class="row bold" style="font-size: 13px;">
            <span>TOTAL</span>
            <span>R$ {{ number_format($pedido->total, 2, ',', '.') }}</span>
        </div>
    </div>

    <div class="line-dashed"></div>

    {{-- FORMAS DE PAGAMENTO --}}
    <div class="mb-2">
        <div class="row bold mb-1">
            <span>PAGAMENTO</span>
            <span></span>
        </div>
        @foreach(($pedido->pagamentos ?? []) as $pag)
        <div class="pagamento-item">
            <span>{{ strtoupper(str_replace('_', ' ', $pag['forma'])) }}</span>
            <span>R$ {{ number_format($pag['valor'], 2, ',', '.') }}</span>
        </div>
        @if(($pag['troco'] ?? 0) > 0)
        <div class="row troco">
            <span>&nbsp;&nbsp;Troco</span>
            <span>R$ {{ number_format($pag['troco'], 2, ',', '.') }}</span>
        </div>
        @endif
        @endforeach
        
        <div class="line-dotted"></div>
        <div class="row bold">
            <span>PAGO TOTAL</span>
            <span>R$ {{ number_format($pedido->total, 2, ',', '.') }}</span>
        </div>
    </div>

    <div class="line-solid"></div>

    {{-- QR CODE PIX (se houver) --}}
    @if(($pedido->qr_code_pix ?? false) || ($pedido->pix_copia_cola ?? false))
    <div class="mb-2 text-center">
        @if($pedido->qr_code_pix ?? false)
        <div class="qr-code">
            {!! $pedido->qr_code_pix !!}
        </div>
        @endif
        @if($pedido->pix_copia_cola ?? false)
        <div class="empresa-dados" style="word-break: break-all;">
            <small>PIX Copia e Cola:</small><br>
            <small>{{ substr($pedido->pix_copia_cola, 0, 100) }}...</small>
        </div>
        @endif
    </div>
    @endif

    {{-- OBSERVAÇÕES --}}
    @if($pedido->observacoes)
    <div class="mb-2">
        <div class="row bold">OBSERVAÇÕES</div>
        <div class="empresa-dados">{{ $pedido->observacoes }}</div>
    </div>
    <div class="line-dashed"></div>
    @endif

    {{-- RODAPÉ --}}
    <div class="footer">
        {{ $configuracao->rodape_cupom ?? '🌟 OBRIGADO PELA PREFERÊNCIA! 🌟' }}<br>
        Volte sempre!
    </div>

    <script>
        // Auto-imprimir se for modo impressão automática
        @if($autoPrint ?? false)
        window.onload = function() {
            window.print();
        }
        @endif
    </script>
</body>
</html>