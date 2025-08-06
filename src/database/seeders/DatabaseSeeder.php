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
        $testUser = User::factory(1)->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
        // テストユーザーを作成

        Address::factory(1)->create([
            'user_id' => $testUser->first()->id,
        ]);

        $this->call([
            CategoriesTableSeeder::class,
            ItemsTableSeeder::class,
        ]);
    }
}
