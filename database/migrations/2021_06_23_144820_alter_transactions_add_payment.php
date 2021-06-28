<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTransactionsAddPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->bigInteger('balance_amount')->after('amount')->default(0)->comment('Piece of amount paid with balance.');
            $table->bigInteger('payment_amount')->after('balance_amount')->default(0)->comment('Piece of amount paid with payment (Credit Card, PIX, Invoice)');
            $table->bigInteger('payment_id')->after('charge_id')->unsigned()->nullable();

            $table->foreign('payment_id')->references('id')->on('payments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['balance_amount', 'payment_amount', 'payment_id']);
        });
    }
}
