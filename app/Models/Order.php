<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id', 'id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'order_id', 'id');
    }

    // public static function orderPaidReports($startDate = null, $endDate = null, $filterPaid = false)
    // {
    //     $orders = Order::with('transaction')
    //         ->whereHas('transaction', function ($transactionQuery) use ($startDate, $endDate, $filterPaid) {

    //             if ($filterPaid) {
    //                 $transactionQuery->where('transactions.status', '=', 'PAID');
    //             } elseif ($filterPaid == false) {
    //                 $transactionQuery->where('transactions.status', '!=', 'PAID');
    //             }

    //             if ($startDate) {
    //                 $startDate = Carbon::parse($startDate, 'Asia/Jakarta')->startOfDay();
    //                 $transactionQuery->where('transactions.created_at', '>=', $startDate);
    //             }

    //             if ($endDate) {
    //                 $endDate = Carbon::parse($endDate, 'Asia/Jakarta')->endOfDay();
    //                 $transactionQuery->where('transactions.created_at', '<=', $endDate);
    //             }
    //         })
    //         ->get();
    //     return $orders;
    // }
}
