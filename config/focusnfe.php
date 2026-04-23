<?php

return [
    'token' => env('FOCUS_NFE_TOKEN', ''),
    'ambiente' => env('FOCUS_NFE_AMBIENTE', 'sandbox'),
    'http' => [
        'timeout' => env('FOCUS_NFE_TIMEOUT', 30),
        'connect_timeout' => env('FOCUS_NFE_CONNECT_TIMEOUT', 10),
    ],
    'urls' => [
        'sandbox' => 'https://homologacao.focusnfe.com.br',
        'production' => 'https://api.focusnfe.com.br',
    ],
    'endpoints' => [
        'nfe' => '/v2/nfe',
        'nfce' => '/v2/nfce',
        'nfse' => '/v2/nfse',
        'carta_correcao' => '/v2/nfe/carta-correcao',
        'inutilizacao' => '/v2/nfe/inutilizacao',
    ],
];
