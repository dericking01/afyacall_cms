<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TodayRevenueTransactionView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement($this->createView());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement($this->dropView());
    }

    public function createView(): string {

        return <<<SQL
            CREATE VIEW today_revenue_transaction_view AS
            SELECT 
                DateCreated,
                SUM(CASE WHEN product_id = 2 THEN amount_IN END) AS sms,
                SUM(CASE WHEN product_id = 1 THEN amount_IN END) AS ivr,
                SUM(CASE WHEN product_id = 3 THEN amount_IN END) AS calls,
                SUM(CASE WHEN product_id = 4 THEN amount_IN END) AS doctor_subs,
                SUM(amount_IN) AS total
            FROM
            (
                SELECT 
                    CAST(created_at AS DATE) AS DateCreated,
                    product_id,
                    amount_IN
                FROM transactions
                WHERE status = 1
            ) AS subquery
            GROUP BY DateCreated;
            SQL;
    }
}
