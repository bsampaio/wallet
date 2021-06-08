<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order')->comment('Unique identifier sent to client.');
            $table->double('amount')->comment('Value of transaction in cents.');
            $table->string('description')->nullable()->comment('Transaction description given by user.');
            $table->integer('status')->comment('0 - Failure, 1 - Success, 2 - Scheduled, 3 - Canceled');
            $table->integer('type')->comment('-1 - Send, 1 - Receive');
            $table->bigInteger('from_id')->unsigned()->comment('Sender wallet');
            $table->bigInteger('to_id')->unsigned()->comment('Receiver wallet');
            $table->integer('retries')->default(0);
            $table->timestamp('last_retry_at')->nullable()->comment('Date and time of last try of transaction.');
            $table->timestamp('scheduled_to')->nullable()->comment('Date and time to execute the transaction.');
            $table->timestamp('confirmed_at')->nullable()->comment('Date and time where the transaction was confirmed.');
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
        Schema::dropIfExists('transactions');
    }
}
