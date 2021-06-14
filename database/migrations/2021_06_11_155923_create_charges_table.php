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
            $table->string('reference')->comment('Unique code who identifies the charge.');
            $table->bigInteger('from_id')->unsigned()->comment('User who you charges FROM.');
            $table->bigInteger('to_id')->unsigned()->comment('User TO receive the transfer.');
            $table->integer('amount')->comment('Amount charged in cents');
            $table->integer('status')->default(1)->comment('Status of the charge. 1 - Open | 2 - Paid | 3 - Cancelled.');
            $table->dateTime('expires_at')->comment('Time where the charge expires.');
            $table->bigInteger('transaction_id')->unsigned()->nullable()->comment('Transaction that pays the charge.');
            $table->string('description')->nullable();
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
