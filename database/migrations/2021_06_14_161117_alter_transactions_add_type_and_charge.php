<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTransactionsAddTypeAndCharge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function(Blueprint $table) {
            // 1 - Transfer | 2 - Charge | 3 - Cashback
            $table->integer('type')->after('order')->comment('Identifies which type of transaction is.')->default(1);
            $table->bigInteger('charge_id')->unsigned()->nullable()->after('to_id')->comment('Indicates if the transaction pays a charge.');

            $table->foreign('charge_id')->references('id')->on('charges');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function(Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('charge_id');
        });
    }
}
