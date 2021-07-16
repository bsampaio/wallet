<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Lifepet\Wallet\SDK\Service\WalletService;
use Tests\TestCase;

class CreditCardTest extends TestCase
{
    const TEST_WALLET_USER = 'customer';
    const HEIMDALL_TEST_KEY = '3bb353bda7b61873c9ebd084c10f2e00718522c4';
    protected $key;
    protected $sdk;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->sdk = new WalletService(self::HEIMDALL_TEST_KEY);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_add_credit_card()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}