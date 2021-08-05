<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraws', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wallet_id');
            $table->bigInteger('amount');
            $table->boolean('authorized')->default(0);
            $table->string('authorization_code')->nullable();
            $table->timestamp('authorized_at');
            $table->string('external_digital_account_id')->nullable();
            $table->string('external_id')->nullable();
            $table->string('external_status')->nullable();
            $table->foreign('wallet_id')->references('id')->on('wallets')->cascadeOnDelete();
            $table->dateTime('scheduled_to');

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
        Schema::dropIfExists('withdraws');
    }
}
