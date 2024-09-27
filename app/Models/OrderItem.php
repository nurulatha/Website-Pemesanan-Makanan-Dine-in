<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OrderItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }

    public static function orderItemReports($startDate = null, $endDate = null)
    {
        $orderItems = OrderItem::with(['order.transaction', 'menu.category'])
            ->whereHas('order', function ($orderQuery) use ($startDate, $endDate) {
                $orderQuery->whereHas('transaction', function ($transactionQuery) use ($startDate, $endDate) {
                    $transactionQuery->where('status', 'PAID');

                    if ($startDate) {
                        $startDate = Carbon::parse($startDate, 'Asia/Jakarta')->startOfDay();
                        $transactionQuery->where('transactions.created_at', '>=', $startDate);
                    }

                    if ($endDate) {
                        $endDate = Carbon::parse($endDate, 'Asia/Jakarta')->endOfDay();
                        $transactionQuery->where('transactions.created_at', '<=', $endDate);
                    }
                });
            })
            ->get();
        return $orderItems;
    }
}
