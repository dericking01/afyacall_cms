<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoctorStasticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctor_stastics', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id')->nullable();
            $table->string('call_date')->nullable();
            $table->string('call_duration')->nullable();
            $table->string('duration')->nullable();
            $table->string('call_talktime')->nullable();
            $table->string('status')->nullable();
            $table->string('channel')->nullable();
            $table->string('call_disconnection')->nullable();
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
        Schema::dropIfExists('doctor_stastics');
    }
}
