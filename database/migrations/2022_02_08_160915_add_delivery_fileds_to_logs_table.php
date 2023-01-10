<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeliveryFiledsToLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->string('status')->after('language')->nullable();
            $table->string('delivery_id')->after('language')->nullable();
            $table->datetime('sent_at')->after('language')->nullable();
            $table->datetime('delivery_at')->after('language')->nullable();
            $table->string('other')->after('language')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropColumn(['status','sent_at','delivery_at','delivery_id','other']);
        });

    }
}

