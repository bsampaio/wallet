<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->bigInteger('from_id')->unsigned();
            $table->bigInteger('to_id')->unsigned();
            $table->integer('amount');
            $table->integer('status')->default(1);
            $table->dateTime('expires_at');
            $table->bigInteger('transaction_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('from_id')->references('id')->on('wallets');
            $table->foreign('to_id')->references('id')->on('wallets');
            $table->foreign('transaction_id')->references('id')->on('transactions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('charges');
    }
}
