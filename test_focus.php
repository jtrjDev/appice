<?php

$token = '6Hr51vMAOXcVn7mTT5D0KMKxAY82QQuN';

$data = [
    "cnpj_emitente" => "19187636000112",
    "data_emissao" => date("Y-m-d\TH:i:sP"),
    "natureza_operacao" => "VENDA AO CONSUMIDOR",
    "presenca_comprador" => "1",
    "modalidade_frete" => "9",
    "itens" => [[
        "numero_item" => 1,
        "codigo_ncm" => "00000000",
        "quantidade_comercial" => 1,
        "quantidade_tributavel" => 1,
        "cfop" => "5102",
        "valor_unitario_comercial" => 10.00,
        "valor_unitario_tributavel" => 10.00,
        "descricao" => "Produto Teste",
        "codigo_produto" => "001",
        "unidade_comercial" => "UN",
        "unidade_tributavel" => "UN",
        "icms_origem" => "0",
        "icms_situacao_tributaria" => "102"
    ]],
    "formas_pagamento" => [[
        "forma_pagamento" => "01",
        "valor_pagamento" => 10.00
    ]]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://homologacao.focusnfe.com.br/v2/nfce?ref=TESTE_" . time());
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Basic ' . base64_encode($token . ':'),
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";
