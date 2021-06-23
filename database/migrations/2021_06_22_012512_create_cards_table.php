<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('hash')->comment('External manager card identifier.');
            $table->boolean('main')->default(0)->comment('Indicates if it\'s the main card.');
            $table->boolean('active')->default(1)->comment('Indicates if the card is active or not.');
            $table->string('number')->comment('Only the last four numbers.');
            $table->string('expiration_month')->comment('Month in MM format.');
            $table->string('expiration_year')->comment('Year in YYYY format.');
            $table->string('nickname')->nullable();
            $table->string('manager')->comment('Tells what service manages the card info.');

            $table->bigInteger('wallet_id')->unsigned();

            $table->timestamps();

            $table->foreign('wallet_id')->references('id')->on('wallets')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards');
    }
}
