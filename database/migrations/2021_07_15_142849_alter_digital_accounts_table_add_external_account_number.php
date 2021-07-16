<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDigitalAccountsTableAddExternalAccountNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('digital_accounts', function (Blueprint $table) {
            $table->string('external_account_number')->after('external_resource_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('digital_accounts', function (Blueprint $table) {
            $table->dropColumn('external_account_number');
        });
    }
}
