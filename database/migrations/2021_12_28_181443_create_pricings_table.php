<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pricings', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('name');
            $table->string('sp_key');
            $table->string('commission_type',1);
            $table->decimal('commission',8,2);
            $table->decimal('k_commission',8,2);
            $table->decimal('a_commission',8,2);
            $table->json('params')->nullable();
            $table->string('bill_fetch',1)->default('N');
            $table->string('category');
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
        Schema::dropIfExists('pricings');
    }
}
