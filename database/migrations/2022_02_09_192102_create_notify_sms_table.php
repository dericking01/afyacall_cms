<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotifySmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notify_sms', function (Blueprint $table) {
            $table->id();
            $table->string('delivery_id');
            $table->integer('contactid')->nullable();
            $table->integer('status')->nullable();
            $table->string('msisdn')->nullable();
            $table->datetime('senttime')->nullable();
            $table->datetime('deliverytime')->nullable();
            $table->string('other')->nullable();
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
        Schema::dropIfExists('notify_sms');
    }
}

