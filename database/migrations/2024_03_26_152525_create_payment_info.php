<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_info', function (Blueprint $table) {
            $table->id();
            $table->string('agent_id')->Nullable();
            $table->string('amount')->Nullable();
            $table->string('currency')->Nullable();
            $table->string('cust_conv_fee')->Nullable();
            $table->string('cou_cust_conv_fee')->Nullable();
            $table->string('biller_id')->Nullable();
            $table->string('ch_id')->Nullable();
            $table->string('mobile_no')->Nullable();
            $table->string('quick_pay')->Nullable();
            $table->string('split_pay')->Nullable();
            $table->string('offus_pay')->Nullable();
            $table->string('payment_mode')->Nullable();
            $table->string('ref_id')->Nullable();
            $table->string('client_request_id')->Nullable();
            $table->string('is_real_time_fetch')->Nullable();
            $table->string('approval_ref_num')->Nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_info');
    }
}
