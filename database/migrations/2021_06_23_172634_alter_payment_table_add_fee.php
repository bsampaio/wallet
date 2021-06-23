<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPaymentTableAddFee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function(Blueprint $table) {
            $table->string('external_payment_id')->nullable()->after('external_charge_id')->comment('External payment identifier');
            $table->date('external_release_date')->nullable()->after('paid_at');
            $table->float('external_fee')->nullable()->after('amount_installments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function(Blueprint $table) {
            $table->dropColumn(['external_payment_id', 'external_release_date', 'external_fee']);
        });
    }
}
