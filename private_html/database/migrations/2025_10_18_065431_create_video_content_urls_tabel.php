<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoContentUrlsTabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_content_urls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url');
            $table->string('trailer_url')->nullable();
            $table->integer('videourl_type_id');
            $table->integer('content_data_id')->nullable();
            $table->integer('skip_intro')->nullable();
            $table->integer('app_id');
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
        Schema::dropIfExists('video_content_urls_tabel');
    }
}
