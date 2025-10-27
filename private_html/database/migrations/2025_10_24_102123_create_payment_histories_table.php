<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->integer('payment_id');
            $table->string('order_id');
            $table->string('razorpay_id');
            $table->string('razorpay_hash')->nullable();
            $table->enum('razorpay_status',array('Success','Fail'))->default('Fail');
            $table->string('razorpay_response');
            $table->integer('amount');
            $table->enum('p_status',array('Payment Initiate','Success','Others'))->default('Payment Initiate');
            $table->datetime('transaction_date');
            $table->enum('sub_status',array('Active','Inactive'))->default('Inactive');
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
        Schema::dropIfExists('payment_histories');
    }
}
