<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hfa')->default(1);
            $table->integer('role_parent_id')->unsigned()->index();
            $table->string('role_name')->index();
            $table->tinyInteger('protected')->default(0);
            $table->integer('owner')->unsigned()->index()->default(1);
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });

       Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hfa')->default(1);
            $table->string('permission_name')->index();
            $table->string('permission_label');
            $table->string('for');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
        });
       Schema::create('roles_and_permissions', function (Blueprint $table) {
           
            $table->integer('role_id')->unsigned()->index();
            $table->integer('permission_id')->unsigned()->index();
            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
            $table->primary(['role_id','permission_id']);
        });
       Schema::create('users_roles', function (Blueprint $table) {
            
            $table->integer('role_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->timestamps();
            $table->primary(['role_id','user_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles_and_permissions');
        Schema::dropIfExists('users_roles');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
        
    }
}
