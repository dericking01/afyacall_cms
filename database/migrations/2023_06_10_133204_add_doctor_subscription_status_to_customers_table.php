<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDoctorSubscriptionStatusToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->integer('doctor_subscription_status')->after('doctor_status')->nullable();
            $table->integer('ivr_subscription_status')->after('ivr_status')->nullable();
            $table->integer('other_status')->after('doctor_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('doctor_subscription_status');
            $table->dropColumn('ivr_subscription_status');
            $table->dropColumn('other_status');
        });
    }
}
