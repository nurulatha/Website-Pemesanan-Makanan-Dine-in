<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Menu extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'menu_id', 'id');
    }

    public static function menuReports($startDate = null, $endDate = null)
    {
        $menus = Menu::with(['orderItems' => function ($orderItemQuery) use ($startDate, $endDate) {
            $orderItemQuery->whereHas('order', function ($orderQuery) use ($startDate, $endDate) {
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
            });
        }, 'category'])->get();

        $menus->each(function ($menu) {
            $menu->total_quantity = $menu->orderItems->sum('quantity');
            $menu->total_sales = $menu->orderItems->sum('total_price');
        });

        return $menus;
    }
}
