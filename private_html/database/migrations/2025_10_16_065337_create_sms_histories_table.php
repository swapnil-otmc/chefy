<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_histories', function (Blueprint $table) {
             $table->bigIncrements('id');
            $table->text('mobile');
            $table->string('sms_type');
            $table->integer('user_id');
            $table->string('message');
            $table->string('message_status');
            $table->datetime('message_deliverytime')->nullable();
            $table->enum('route',array('1','4'))->default('4');
            $table->enum('sms_api',array('Msg91','Airtel'))->default('Airtel');
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
        Schema::dropIfExists('sms_histories');
    }
}
