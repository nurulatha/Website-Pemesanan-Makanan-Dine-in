<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public static function reports($startDate = null, $endDate = null, $tableId = null)
    {
        $orderItems = OrderItem::with('menu.category')
            ->join('transactions', 'order_items.order_id', '=', 'transactions.order_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('transactions.status', 'PAID');

        if ($startDate) {
            $startDate = Carbon::parse($startDate, 'Asia/Jakarta')->startOfDay();
            $orderItems->where('transactions.updated_at', '>=', $startDate);
        }

        if ($endDate) {
            $endDate = Carbon::parse($endDate, 'Asia/Jakarta')->endOfDay();
            $orderItems->where('transactions.updated_at', '<=', $endDate);
        }

        if ($tableId) {
            $orderItems->where('orders.table_id', $tableId);
        }

        $orderItems = $orderItems->select(
            'order_items.id',
            'order_items.quantity',
            'order_items.total_price',
            'transactions.status',
            'order_items.order_id',
            'order_items.menu_id',
            'orders.table_id',
            'orders.customer_name'
        )
            ->get();

        return $orderItems;
    }

    public static function orderReports()
    {
        $orders = Order::join('transactions', 'order_items.order_id', '=', 'transactions.order_id');
    }
}
