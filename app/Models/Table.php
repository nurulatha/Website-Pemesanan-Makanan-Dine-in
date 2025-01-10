<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function status()
    {
        return $this->belongsTo(TableStatus::class, 'table_status_id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'table_id', 'id');
    }

    public static function tableReports($startDate = null, $endDate = null)
    {
        $tables = Table::with(['orders' => function ($orderQuery) use ($startDate, $endDate) {
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
        }, 'orders.orderItems'])
            ->get();

        $tables->each(function ($table) {
            $table->total_orders = $table->orders->count();
            $table->total_sales = $table->orders->flatMap(function ($order) {
                return $order->orderItems;
            })->sum('total_price');
        });

        return $tables;
    }
}
