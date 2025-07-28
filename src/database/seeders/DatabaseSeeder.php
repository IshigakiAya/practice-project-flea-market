<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(1)->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        // 1件のダミーユーザーを作成

        $this->call([
            CategoriesTableSeeder::class,
            ItemsTableSeeder::class,
        ]);

        Address::factory(1)->create();
    }
}
