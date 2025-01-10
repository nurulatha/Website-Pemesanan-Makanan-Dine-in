<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Config;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Role;
use App\Models\Table;
use App\Models\TableStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('role_users')->truncate();
        Role::truncate();
        User::truncate();
        Category::truncate();
        Menu::truncate();
        TableStatus::truncate();
        Table::truncate();
        Order::truncate();
        OrderItem::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $roles = ['admin', 'waiter', 'cashier', 'manager'];

        foreach ($roles as $index => $roleName) {
            $role = Role::create([
                'name' => $roleName,
            ]);

            $roleIds[] = $role->id;
        }

        $names = ['admin', 'waiter', 'cashier', 'manager'];
        // $userPass = ['admin', 'waiter', 'cashier', 'manager'];

        foreach ($names as $index => $name) {
            $user = User::create([
                'name' => $name,
                'username' => $name,
                'password' => 'password',
            ]);

            DB::table('role_users')->insert([
                'role_id' => $roleIds[$index],
                'user_id' => $user->id,
            ]);
        }

        $categories = ['Sushi', 'Sashimi', 'Ramen', 'Donburi', 'Yakitori', 'Udon', 'Drinks'];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
            ]);
        }

        Menu::factory(30)->create();

        $tableStatuses = ['Kosong', 'Terisi', 'Sedang Dipesan'];

        foreach ($tableStatuses as $tableStatus) {
            TableStatus::create([
                'name' => $tableStatus,
            ]);
        }

        for ($i = 0; $i < 3; $i++) {
            Table::create([
                'url' => uniqid(),
                'table_status_id' => 1,
            ]);
        }

        Config::create([
            'name' => 'xendit',
            'value' => [
                'XENDIT_API_KEY' => 'xnd_development_rcEG4al3Tdsah0dMdqscSGDZGqIz4jSYVN8UJhmrLnfrYKcSHYFexzyCvs2i',
                'XENDIT_CALLBACK_TOKEN' => 'ERqqHEhg5KPl5wcSBZbcR856ZAEeHhZ3bXNIJLat3wRnPEfK',
                'REDIRECT_URL' => 'http://192.168.0.50:8081/',
            ],
        ]);
    }
}
