<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
           $table->bigIncrements('id');
            $table->string('code')->unique(); // Alphanumeric code for the coupon Make it unique
            $table->integer('discount')->default(100); // Discount percentage (e.g., 10 for 10%)
            $table->integer('duration')->default(30); // How many days the coupon is valid (Default 30 days)
            $table->integer('uses')->default(1); // How Many times the coupon can be used (Default 1)
            $table->timestamp('expiry_date')->nullable(); // Date until which the coupon is valid
            $table->timestamp('last_used')->nullable(); // Date when the coupon was last used
            $table->enum('status', ['A', 'D', 'I'])->default('A');
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
        Schema::dropIfExists('coupons');
    }
}
