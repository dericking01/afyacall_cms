<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObdDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('obd_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('deliveryid')->index('deliveryid');
            $table->integer('obdid')->nullable();
            $table->integer('groupid')->nullable();
            $table->integer('contactid')->nullable();
            $table->string('msisdn')->nullable();
            $table->string('status')->nullable();
            $table->datetime('senttime')->nullable();
            $table->datetime('deliverytime')->nullable();
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
        Schema::dropIfExists('obd_deliveries');
    }
}

