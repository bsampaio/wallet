<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTransferTablesAddProcessedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('withdraws', function(Blueprint $table) {
            $table->timestamp('processed_at')->nullable();
        });
        Schema::table('transfers', function(Blueprint $table) {
            $table->timestamp('processed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('withdraws', function(Blueprint $table) {
           $table->dropColumn('processed_at');
        });
        Schema::table('transfers', function(Blueprint $table) {
            $table->dropColumn('processed_at');
        });
    }
}
