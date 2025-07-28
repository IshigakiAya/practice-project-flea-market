<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory; // 日本語の住所を生成するため

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Fakerのインスタンスを日本語ロケールで明示的に作成
        $faker = FakerFactory::create('ja_JP');

        // 既存のユーザーの中からランダムに1人選ぶ
        // もしユーザーがまだ一人もいない場合は、ここで新しいユーザーを作成
        $userId = User::inRandomOrder()->first()->id ?? User::factory()->create()->id;

        return [
            'user_id' => $userId, // 既存のユーザーIDと紐付ける
            'postal_code' => $faker->postcode(),
            'address' => $faker->prefecture() . $faker->city() . $faker->streetAddress(),
            'building' => $faker->optional()->secondaryAddress(), // null許容(ランダムでnullも生成)
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
