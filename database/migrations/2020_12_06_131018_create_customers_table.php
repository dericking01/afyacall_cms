<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('msisdn')->nullable();
            $table->string('keyword')->nullable();
            $table->string('content')->nullable();
            $table->string('fullname')->nullable();
            $table->string('language')->default('sw');
            $table->integer('enticement')->default(0);
            $table->integer('ivr_enticement')->default(0);
            $table->integer('doctor_enticement')->default(0);
            $table->string('source')->default('MNO');
            $table->dateTime('registered_at')->nullable();
            $table->integer('product_id')->nullable();
            $table->integer('status')->nullable();
            $table->integer('ivr_status')->nullable();
            $table->integer('doctor_status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
