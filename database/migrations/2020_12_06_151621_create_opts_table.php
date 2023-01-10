<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opts', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_ID')->nullable();
            $table->integer('product_ID')->nullable();
            $table->tinyInteger('opt_value')->nullable();
            $table->string('keyword')->nullable();
            $table->string('content')->nullable();
            $table->string('OriginatorConversationID')->nullable();
            $table->string('ConversationID')->nullable();
            $table->string('date')->nullable();
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
        Schema::dropIfExists('opts');
    }
}
