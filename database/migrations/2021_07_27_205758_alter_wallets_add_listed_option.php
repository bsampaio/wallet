<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterWalletsAddListedOption extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wallets', function(Blueprint $table) {
             $table->boolean('listed')->default(1)->comment('Defines if the wallet will be shown at the search results.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wallets', function(Blueprint $table) {
            $table->dropColumn('listed');
        });
    }
}
