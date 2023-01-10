<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRevenueView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement($this->createRevenuesView());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement($this->dropRevenueView());
    }


    private function createRevenuesView(): string
    {
        return <<<SQL
                CREATE VIEW view_revenue_data AS
                SELECT 
                   CAST(E.created_at AS DATE) DateCreated,
                    SUM(case when E.product_id = 2 and E.status = 1 then E.amount_IN end) as sms,
                    SUM(case when E.product_id = 1 and E.status = 1 then E.amount_IN end) as ivr,
                    SUM(case when E.product_id = 3 and E.status = 1 then E.amount_IN end) as calls,
                    SUM(case when E.status = 1 then E.amount_IN end) as total
                    
                FROM    transactions E
                GROUP   BY CAST(E.created_at AS DATE)
            SQL;
    }

    private function dropRevenueView(): string
    {
        return <<<SQL
            DROP VIEW IF EXISTS 'view_revenue_data';
            SQL;
    }
}

