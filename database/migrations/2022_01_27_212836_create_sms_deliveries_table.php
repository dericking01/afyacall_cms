<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('deliveryid');
            $table->integer('campaignid')->nullable();
            $table->integer('groupid')->nullable();
            $table->integer('contactid')->nullable();
            $table->integer('status')->nullable();
            $table->string('msisdn')->nullable();
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
        Schema::dropIfExists('sms_deliveries');
    }
}
