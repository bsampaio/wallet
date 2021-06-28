<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('payment_type')->comment('1 - Credit Card, 2 - Pix, 3 - Invoice');
            $table->bigInteger('amount')->comment('Amount to charge.');
            $table->bigInteger('original_amount')->comment('Original amount value without taxes');
            $table->integer('installments')->comment('Number of installments for payment.');
            $table->float('amount_installments')->comment('Amount divided by installments');
            $table->integer('status')->comment('-1 - FAILED, 0 - OPEN, 1 - CONFIRMED');
            $table->string('manager')->comment('Tells what gateway will proccess the payment.');

            //Billing address info.
            $table->string('street');
            $table->string('number');
            $table->string('complement')->nullable();
            $table->string('neighborhood');
            $table->string('city');
            $table->string('state');
            $table->string('post_code');

            //External info.
            $table->string('external_transaction_id')->nullable();
            $table->string('external_charge_id');
            $table->string('external_checkout_url');

            //Transaction info.
            $table->string('fail_reason')->nullable();
            $table->dateTime('paid_at')->nullable();

            //Wallet info.
            $table->bigInteger('wallet_id')->unsigned();
            $table->foreign('wallet_id')->references('id')->on('wallets');

            //Credit card info.
            $table->bigInteger('card_id')->unsigned()->nullable();
            $table->foreign('card_id')->references('id')->on('cards');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
