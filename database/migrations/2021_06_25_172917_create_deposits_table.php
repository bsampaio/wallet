<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->integer('payment_type');
            $table->bigInteger('amount');
            $table->bigInteger('original_amount');
            $table->float('external_fee');
            $table->integer('status')->default(1);
            $table->timestamp('confirmed_at')->nullable();
            $table->string('external_transaction_id')->nullable();
            $table->string('external_payment_id')->nullable();
            $table->string('external_charge_id')->nullable();
            $table->string('fail_reason')->nullable();
            $table->bigInteger('wallet_id')->unsigned();

            $table->foreign('wallet_id')->references('id')->on('wallets');
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
        Schema::dropIfExists('deposits');
    }
}
