<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubCategoryTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_category_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('poster')->nullable();
            $table->enum('styleType',array('Horizontal','Vertical'))->default('Horizontal');
            $table->enum('thumbSize',array('High','Medium','Low'))->default('Medium');
            $table->integer('priority');
            $table->integer('home_priority')->nullable();
            $table->integer('category_id')->nullable();
            $table->enum('status', array('A','I','D'))->default('A');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_category_types');
    }
}
