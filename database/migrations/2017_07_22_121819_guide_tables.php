<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GuideTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::create('guide_steps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable(); // nested steps
            $table->integer('guide_step_type_id')->unsigned()->nullable(); // disposition, etc.
            $table->string('name'); // upload document, send for approval, etc.
            $table->tinyInteger('hfa')->default(0);

            $table->foreign('parent_id')->references('id')->on('guide_steps');
            $table->foreign('guide_step_type_id')->references('id')->on('guide_step_types');
        });

        Schema::create('guide_step_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name'); // disposition, parcel, etc...
        });

        Schema::create('guide_progress', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('guide_step_id')->unsigned()->nullable();
            $table->integer('type_id')->unsigned()->nullable(); // the disposition id for example
            $table->tinyInteger('started')->default(0);
            $table->tinyInteger('completed')->default(0);
            $table->timestamps();

            $table->foreign('guide_step_id')->references('id')->on('guide_steps');
        });
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $step_types = array(       
            array( 
                'name' => 'disposition'
            )
        );
        DB::table('guide_step_types')->insert($step_types);

        $guide_steps = array(       
            array( // 1
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 1',
                'hfa' => 0
            ),    
            array( 
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Complete form',
                'hfa' => 0
            ),    
            array( 
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Upload supporting documents',
                'hfa' => 0
            ),    
            array( 
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Submit for internal approval',
                'hfa' => 0
            ),    
            array( 
                'parent_id' => 1,
                'guide_step_type_id' => 1,
                'name' => 'Submit to HFA',
                'hfa' => 0
            ),

            array( //6
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 2',
                'hfa' => 1
            ),    
            array( 
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Confirm calculations',
                'hfa' => 1
            ),  
            array( 
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Review supporting documents',
                'hfa' => 1
            ),  
            array( 
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Approve request',
                'hfa' => 1
            ),  
            array( 
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Notify Landbank',
                'hfa' => 1
            ),  
            array( 
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Request lien release',
                'hfa' => 1
            ),  
            array( 
                'parent_id' => 6,
                'guide_step_type_id' => 1,
                'name' => 'Add to disposition invoice',
                'hfa' => 1
            ),

            array( //13
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 3',
                'hfa' => 1
            ),
            array( 
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Fiscal agent release lien',
                'hfa' => 1
            ),
            array( 
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Begin sale of parcel',
                'hfa' => 0
            ),
            array( 
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Finalize sale',
                'hfa' => 0
            ),
            array( 
                'parent_id' => 13,
                'guide_step_type_id' => 1,
                'name' => 'Upload final executed release',
                'hfa' => 0
            ),

            array( //18
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 4 HFA',
                'hfa' => 1
            ),
            array( 
                'parent_id' => 18,
                'guide_step_type_id' => 1,
                'name' => 'Review disposition',
                'hfa' => 1
            ),
            array( 
                'parent_id' => 18,
                'guide_step_type_id' => 1,
                'name' => 'Holly approval',
                'hfa' => 1
            ),
            array( 
                'parent_id' => 18,
                'guide_step_type_id' => 1,
                'name' => 'Jim approval',
                'hfa' => 1
            ),
            array( 
                'parent_id' => 18,
                'guide_step_type_id' => 1,
                'name' => 'Send invoice',
                'hfa' => 1
            ),

            array( //23
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 5 LB',
                'hfa' => 0
            ),
            array( 
                'parent_id' => 23,
                'guide_step_type_id' => 1,
                'name' => 'Send invoice payment',
                'hfa' => 0
            ),

            array( //25
                'parent_id' => null,
                'guide_step_type_id' => 1,
                'name' => 'Step 6 HFA',
                'hfa' => 1
            ),
            array( 
                'parent_id' => 25,
                'guide_step_type_id' => 1,
                'name' => 'Mark as paid',
                'hfa' => 1
            )
        );
        DB::table('guide_steps')->insert($guide_steps);
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::dropIfExists('guide_steps');
        Schema::dropIfExists('guide_step_types');
        Schema::dropIfExists('guide_progress');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
