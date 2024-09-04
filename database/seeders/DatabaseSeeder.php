<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Role;
use App\Models\Status;
use App\Models\Table;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('role_users')->truncate();
        Role::truncate();
        User::truncate();
        Category::truncate();
        Menu::truncate();
        Status::truncate();
        Table::truncate();
        Order::truncate();
        OrderItem::truncate();
        Cart::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $roles = ['Admin', 'Waiter', 'Cashier', 'Manager'];

        foreach ($roles as $index => $roleName) {
            $role = Role::create([
                'name' => $roleName
            ]);

            $roleIds[] = $role->id;
        }

        $names = ['Admin', 'Waiter', 'Cashier', 'Manager'];
        $userPass = ['admin', 'waiter', 'cashier', 'manager'];

        foreach ($names as $index => $name) {
            $user = User::create([
                'name' => $name,
                'username' => $userPass[$index],
                'password' => $userPass[$index]
            ]);

            DB::table('role_users')->insert([
                'role_id' => $roleIds[$index],
                'user_id' => $user->id
            ]);
        }

        $categories = ['Sushi', 'Sashimi', 'Ramen', 'Donburi', 'Yakitori', 'Udon', 'Drinks'];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category
            ]);
        }

        Menu::factory(30)->create();

        $statuses = ['Kosong', 'Terisi', 'Sedang Dipesan'];

        foreach ($statuses as $status) {
            Status::create([
                'name' => $status,
            ]);
        }

        $urls = ['meja-satu', 'meja-dua', 'meja-tiga'];

        foreach ($urls as $url) {
            Table::create([
                'url' => $url,
                'status_id' => 1
            ]);
        }
    }
}
