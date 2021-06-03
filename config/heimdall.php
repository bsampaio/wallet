<?php
/**
 * Created by PhpStorm.
 * User: Breno
 * Date: 03/06/2021
 * Time: 14:27
 */
return [
    'allowed' => [
        [
            'app'    => 'lifepet-customer',
            'key'    => '4548c387e33bae1be499f743ce2cb2fa172c2d0e',
            'origin' => ['customer.lifepet.com.br'],
        ],
        [
            'app' => 'lifepet-partner',
            'key' => 'e970dca504f57d7a8aa0fee5893f27fedd0d1a35',
            'origin' => ['partner.lifepet.com.br'],
        ],
        [
            'app' => 'lifepet-staging',
            'key' => '3bb353bda7b61873c9ebd084c10f2e00718522c4',
            'origin' => ['staging.lifepet.com.br', 'localhost', '127.0.0.1'],
        ],
    ]
];

