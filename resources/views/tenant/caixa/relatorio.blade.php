<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Fechamento - Caixa #{{ $caixa->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: monospace;
            width: 280px;
            margin: 0 auto;
            padding: 12px;
            font-size: 11px;
            line-height: 1.3;
            background: white;
        }
        @media print { .no-print { display: none; } }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .line-dashed { border-top: 1px dashed #000; margin: 6px 0; }
        .line-solid { border-top: 2px solid #000; margin: 6px 0; }
        .row { display: flex; justify-content: space-between; margin: 3px 0; }
        .footer { text-align: center; font-size: 9px; margin-top: 10px; }
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
        .text-green { color: #2ecc71; }
        .text-red { color: #e74c3c; }
    </style>
</head>
<body>
    <div class="text-center">
        <div class="bold">{{ $config->razao_social ?? 'ESTABELECIMENTO' }}</div>
        <div>CNPJ: {{ $config->cpf_cnpj_formatado ?? '' }}</div>
        <div>{{ $config->endereco ?? '' }}, {{ $config->numero ?? '' }}</div>
        <div class="bold">RELATÓRIO DE FECHAMENTO</div>
    </div>

    <div class="line-dashed"></div>

    <div class="row">
        <span>CAIXA:</span>
        <span class="bold">#{{ $caixa->id }}</span>
    </div>
    <div class="row">
        <span>DATA ABERTURA:</span>
        <span>{{ \Carbon\Carbon::parse($caixa->aberto_em)->format('d/m/Y H:i') }}</span>
    </div>
    <div class="row">
        <span>DATA FECHAMENTO:</span>
        <span>{{ $caixa->fechado_em ? \Carbon\Carbon::parse($caixa->fechado_em)->format('d/m/Y H:i') : '-' }}</span>
    </div>
    <div class="row">
        <span>OPERADOR:</span>
        <span>{{ $caixa->operador->name ?? '' }}</span>
    </div>

    <div class="line-dashed"></div>

    <div class="row bold">
        <span>RESUMO DE VENDAS</span>
        <span></span>
    </div>
    <div class="row">
        <span>Quantidade de vendas:</span>
        <span>{{ $caixa->quantidade_vendas }}</span>
    </div>
    <div class="row">
        <span>Total vendido:</span>
        <span>R$ {{ number_format($caixa->total_vendas, 2, ',', '.') }}</span>
    </div>

    <div class="line-dashed"></div>

    <div class="row bold">
        <span>FORMAS DE PAGAMENTO</span>
        <span></span>
    </div>
    <div class="row">
        <span>Dinheiro:</span>
        <span>R$ {{ number_format($caixa->total_dinheiro, 2, ',', '.') }}</span>
    </div>
    <div class="row">
        <span>Cartão Crédito:</span>
        <span>R$ {{ number_format($caixa->total_credito, 2, ',', '.') }}</span>
    </div>
    <div class="row">
        <span>Cartão Débito:</span>
        <span>R$ {{ number_format($caixa->total_debito, 2, ',', '.') }}</span>
    </div>
    <div class="row">
        <span>PIX:</span>
        <span>R$ {{ number_format($caixa->total_pix, 2, ',', '.') }}</span>
    </div>

    <div class="line-solid"></div>

    <div class="row bold">
        <span>TOTAL GERAL</span>
        <span>R$ {{ number_format($caixa->total_vendas, 2, ',', '.') }}</span>
    </div>

    <div class="line-dashed"></div>

    <div class="row bold">
        <span>FECHAMENTO</span>
        <span></span>
    </div>
    <div class="row">
        <span>Saldo inicial:</span>
        <span>R$ {{ number_format($caixa->saldo_inicial, 2, ',', '.') }}</span>
    </div>
    <div class="row">
        <span>Saldo esperado:</span>
        <span>R$ {{ number_format($caixa->saldo_inicial + $caixa->total_vendas, 2, ',', '.') }}</span>
    </div>
    <div class="row">
        <span>Saldo declarado:</span>
        <span>R$ {{ number_format($caixa->saldo_final ?? 0, 2, ',', '.') }}</span>
    </div>
    @php
        $saldoEsperado = $caixa->saldo_inicial + $caixa->total_vendas;
        $diferenca = ($caixa->saldo_final ?? 0) - $saldoEsperado;
    @endphp
    <div class="row">
        <span>Diferença:</span>
        <span class="{{ $diferenca > 0 ? 'text-green' : ($diferenca < 0 ? 'text-red' : '') }}">
            R$ {{ number_format(abs($diferenca), 2, ',', '.') }}
            {{ $diferenca > 0 ? '(sobra)' : ($diferenca < 0 ? '(falta)' : '') }}
        </span>
    </div>

    @if($caixa->observacao)
    <div class="line-dashed"></div>
    <div class="row">
        <span>OBS:</span>
        <span>{{ $caixa->observacao }}</span>
    </div>
    @endif

    <div class="line-dashed"></div>

    <div class="footer">
        <div>________________________________</div>
        <div>Assinatura do Operador</div>
        <div>{{ $caixa->fechado_em ? \Carbon\Carbon::parse($caixa->fechado_em)->format('d/m/Y H:i:s') : '' }}</div>
        <div>** Este documento é um resumo das operações do caixa **</div>
    </div>

    <button class="btn-print no-print" onclick="window.print()">🖨️ Imprimir Relatório</button>

    <script>
        @if($autoPrint)
        window.onload = function() {
            window.print();
        }
        @endif
    </script>
</body>
</html>