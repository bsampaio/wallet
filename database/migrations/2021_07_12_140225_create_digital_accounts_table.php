<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDigitalAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('digital_accounts', function (Blueprint $table) {
            $table->id();

            $table->integer('status')->default('0');

            $table->string('type')->default('PAYMENT')->comment('PAYMENT');
            $table->string('name');
            $table->string('document')->comment('Numeric only document.')->unique();
            $table->string('email');
            $table->date('birth_date')->nullable();
            $table->string('phone');
            $table->string('business_area');
            $table->string('lines_of_business');

            /**
             * Address
             */
            $table->string('street');
            $table->string('number');
            $table->string('complement')->nullable();
            $table->string('neighborhood');
            $table->string('city');
            $table->string('state');
            $table->string('post_code');
            $table->string('ibge')->nullable();

            /**
             * Bank Account
             */
            $table->string('bank_number');
            $table->string('agency_number');
            $table->string('account_number');
            $table->string('account_complement_number')->nullable()->comment('Only required for CAIXA accounts.');
            $table->string('account_type')->comment('CHECKINGS, SAVINGS');
            $table->string('account_holder_name');
            $table->string('account_holder_document');

            $table->double('monthly_income_or_revenue');

            /**
             * PJ
             */
            $table->string('cnae')->nullable();
            $table->string('company_type')->nullable();
            $table->date('establishment_date')->nullable();

            $table->unsignedBigInteger('wallet_id')->unique();
            $table->foreign('wallet_id')->references('id')->on('wallets');

            /**
             * Juno external data management
             */
            $table->string('external_id')->nullable();
            $table->string('external_type')->nullable();
            $table->string('external_status')->nullable();
            $table->string('external_document')->nullable();
            $table->string('external_resource_token')->nullable();
            $table->dateTime('external_created_at')->nullable();

            $table->string('manager')->default('JUNO');
            $table->timestamps();
        });

        Schema::create('digital_accounts_legal_representatives', function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('document');
            $table->date('birth_date');
            $table->string('mother_name');
            $table->string('type');
            $table->bigInteger('digital_account_id')->unsigned();
            $table->foreign('digital_account_id', 'dig_acc_id_foreign')->references('id')->on('digital_accounts')->cascadeOnDelete();
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
        Schema::dropIfExists('digital_accounts_legal_representatives');
        Schema::dropIfExists('digital_accounts');
    }
}
