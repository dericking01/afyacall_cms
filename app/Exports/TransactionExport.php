<?php

namespace App\Exports;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $startDate = Carbon::yesterday();
        $sql = DB::table('transactions')
            ->join('customers', 'customers.id', '=', 'transactions.customer_ID')
            ->join('products', 'products.id', '=', 'transactions.product_id')
            ->whereDate('transactions.created_at', '>=', $startDate->startOfDay())
            ->whereDate('transactions.created_at', '<=', $startDate->endOfDay())
            ->where('transactions.status',1)
            ->select('customers.msisdn as Customer Number', 'transactions.created_at as Transcation Date', 'transactions.amount_IN as Amount', 'transactions.currency as Method', 'transactions.status', 'products.name', 'transactions.response as Response')
            ->get();

        return $sql;
    }

    public function headings(): array
    {
        return [
          'Customer Number',
          'Transcation Date',
          'Amount',
          'Method',
          'Status',
          'Product',
          'Response'
        ];
    }
}
