<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable();
            $table->integer('app_id')->nullable();
            $table->text('description');
            $table->string('order_id')->nullable();
            $table->string('image')->nullable();
            $table->string('currency');
            $table->integer('coupon_id');
            $table->integer('amount');
            $table->enum('p_status',array('Payment Initiate','Success','Others'))->default('Payment Initiate');
            $table->datetime('sub_start_date'); 
            $table->datetime('sub_end_date');
            $table->datetime('Transaction_date'); 
            $table->enum('sub_status',array('Active','Inactive'))->default('Inactive');
            $table->enum('cancel_status', array('true', 'false'))->default('false');
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
        Schema::dropIfExists('payments');
    }
}
