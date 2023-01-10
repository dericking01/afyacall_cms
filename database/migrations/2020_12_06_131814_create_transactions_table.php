<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->date('transaction_date')->nullable();
            $table->double('amount_IN')->default(0);
            $table->string('token')->nullable();
            $table->string('currency')->default('TZS');
            $table->string('transaction_type')->nullable();
            $table->string('response')->nullable();
            $table->string('status')->nullable();
            $table->string('conventions_ID')->nullable();
            $table->string('response_code')->nullable();
            $table->string('receipt')->nullable();
            $table->integer('customer_ID')->nullable();
            $table->integer('subscription_ID')->nullable();
            $table->integer('content_ID')->nullable();
            $table->integer('product_id')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
