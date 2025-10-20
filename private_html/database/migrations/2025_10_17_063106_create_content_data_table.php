<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_data', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('description');
            $table->integer('sub_category_id');
            $table->integer('category_type_id');
            $table->integer('videourl_type_id');
            $table->string('starcast');
            $table->string('director');
            $table->string('crew')->nullable();
            $table->integer('content_type_id');
            $table->string('tags')->nullable();
            $table->string('genre');
            $table->text('sub_description');
            $table->string('pg_rating_id');
            $table->integer('language_id');
            $table->string('release_date')->nullable();
            $table->text('duration');
            $table->enum('status',array('A','D','I'))->default('A');
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
        Schema::dropIfExists('content_data');
    }
}
