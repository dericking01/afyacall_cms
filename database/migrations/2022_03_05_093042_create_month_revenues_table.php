<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMonthRevenuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement($this->createMonthlyRevenues());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement($this->dropMonthlyRevenue());

    }

    private function createMonthlyRevenues(): string
    {
        return <<<SQL
                CREATE VIEW month_revenues AS
                SELECT 
                    DATE_FORMAT(E.created_at,'%M') AS Monthly,
                    SUM(case when E.product_id = 2 and E.status = 1 then E.amount_IN else 0 end) as sms,
                    SUM(case when E.product_id = 1 and E.status = 1 then E.amount_IN else 0 end) as ivr,
                    SUM(case when E.product_id = 3 and E.status = 1 then E.amount_IN else 0 end) as calls,
                    SUM(case when E.status = 1 then E.amount_IN else 0 end) as total,
                    SUM(case when E.product_id = 2 and E.status = 1 then E.amount_IN else 0 end) / 150 as sms_sub,
                    SUM(case when E.product_id = 1 and E.status = 1 then E.amount_IN else 0 end) / 300 as ivr_sub,
                    SUM(case when E.product_id = 3 and E.status = 1 then E.amount_IN else 0 end) / 3000 as calls_sub,
                    ((SUM(case when E.product_id = 2 and E.status = 1 then E.amount_IN else 0 end) / 150) + (SUM(case when E.product_id = 1 and E.status = 1 then E.amount_IN else 0 end) / 300) + (SUM(case when E.product_id = 3 and E.status = 1 then E.amount_IN else 0 end) / 3000 )) as total_sub
                    
                FROM    transactions E
                GROUP   BY  DATE_FORMAT(E.created_at,'%M')
            SQL;
    }
    private function dropMonthlyRevenue(): string
    {
        return <<<SQL
            DROP VIEW IF EXISTS 'month_revenues';
            SQL;
    }
}

