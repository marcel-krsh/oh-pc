<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
        Schema::create('entities', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('entity_name');
            $table->integer('user_id')->unsigned()->index()->default(2);
            $table->integer('active')->default(1);
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->integer('state_id');
            $table->string('zip')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('web_address')->nullable();
            $table->string('email_address');
            $table->string('datatran_user')->nullable();
            $table->string('datatran_password')->nullable();
            $table->string('logo_link')->nullable();
            $table->string('owner_type')->default('user');
            $table->integer('owner_id')->default(2);
            $table->timestamps();
            $table->index('owner_id');
        });

        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('account_name');
            $table->integer('entity_id')->unsigned()->index();
            $table->string('owner_type')->default('program');
            $table->integer('owner_id')->unsigned()->default(2)->nullable();
            $table->integer('account_type_id')->unsigned()->default(1)->nullable();
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
            $table->index('owner_id');
            $table->index('account_type_id');
        });
         Schema::create('account_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 250);
            $table->integer('active'); 
            $table->timestamps();
        });
        Schema::create('transaction_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('category_name', 250);
            $table->integer('active'); 
            $table->timestamps();
        });
          Schema::create('transaction_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type_name',250);
            $table->integer('active');
            $table->timestamps();
        });
           Schema::create('transaction_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('status_name');
            $table->integer('active');
            $table->timestamps();
        });
        
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id')->unsigned()->index();
            $table->char('credit_debit',1);
            $table->float('amount',12,2);
            $table->integer('transaction_category_id')->unsigned()->nullable();
            $table->integer('type_id')->nullable();
            $table->integer('link_to_type_id')->nullable(); // this is the id of the type this is linked to - ie the invoice
            $table->integer('status_id')->index()->nullable();
            $table->integer('owner_id')->unsigned()->nullable();
            $table->string('owner_type')->default('account');
            $table->date('date_entered');
            $table->date('date_cleared')->nullable();
            $table->text('transaction_note')->nullable();
            $table->timestamps();
            $table->index('transaction_category_id');
            $table->index('link_to_type_id');
            $table->index('owner_id');
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
        Schema::dropIfExists('transaction_categories');
        Schema::dropIfExists('transaction_statuses');
        Schema::dropIfExists('transaction_types');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('account_types');
        Schema::dropIfExists('entities');
        
        
        
        
    }
}
