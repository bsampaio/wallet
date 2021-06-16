<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterWalletsAddType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->smallInteger('type')->unsigned()->default(1)->after('user_id')->comment('Describes if the account is a Business or a Personal account.');
            $table->integer('cashback')->default(0)->comment('Defines in integer the fixed amount percentage of cashback on a given transaction');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn(['type', 'cashback']);
        });
    }
}
