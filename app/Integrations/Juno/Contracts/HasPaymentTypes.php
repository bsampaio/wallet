<?php


namespace App\Integrations\Juno\Contracts;


interface HasPaymentTypes
{
    const PAYMENT_TYPE__BOLETO = 'BOLETO';
    const PAYMENT_TYPE__BOLETO_PIX = 'BOLETO_PIX';
    const PAYMENT_TYPE__CREDIT_CARD = 'CREDIT_CARD';
}
