<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTransactionsAddOriginId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function(Blueprint $table) {
            $table->bigInteger('origin_id')->unsigned()->nullable()->after('to_id')->comment('Indicates if the transaction is a derivative of another transaction.');
            $table->foreign('origin_id')->references('id')->on('transactions')->cascadeOnDelete();
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
            $table->dropColumn('origin_id');
        });
    }
}
