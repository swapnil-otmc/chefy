<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_contents', function (Blueprint $table) {
           $table->bigIncrements('id');
            $table->integer('content_data_id');
            $table->integer('app_data_id');
            $table->integer('priority');
            $table->integer('category_id');
            $table->integer('sub_category_id');
            $table->integer('content_types_id');
            $table->integer('content_title_id')->nullable();
            $table->integer('sub_category_priority')->nullable();
            $table->enum('status',array('A','I','D'))->default('A');
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
        Schema::dropIfExists('app_contents');
    }
}
