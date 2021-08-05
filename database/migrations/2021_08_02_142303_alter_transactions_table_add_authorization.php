<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTransactionsTableAddAuthorization extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('requires_documentation')->default(0)->after('retries');
            $table->integer('documentation_status')->after('requires_documentation')->default(0);
            $table->timestamp('documentation_sent_at')->after('documentation_status')->nullable();
            $table->timestamp('compensation_authorized_at')->after('documentation_sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['requires_documentation', 'documentation_status', 'documentation_sent_at', 'compensation_authorized_at']);
        });
    }
}
