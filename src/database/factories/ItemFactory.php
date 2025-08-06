<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        $userId = User::inRandomOrder()->first() ?? User::factory()->create();

        return [
            'user_id' => $userId,
            'name' => $this->faker->word, //ダミーの商品名
            'price' => $this->faker->numberBetween(0, 1000000),
            'description' => $this->faker->sentence, // ダミーの商品説明
            'condition' => $this->faker->randomElement(['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い']),
            'image' => 'items/dummy_image.jpg', // ダミーの画像パス
            'status' => 'active',
        ];
    }

    // カテゴリと紐付けるためのafterCreatingコールバック
    public function configure()
    {
        return $this->afterCreating(function (Item $item) {
            // Seederが作成した既存のカテゴリからランダムに1つ選ぶ
            $category = category::inRandomOrder()->first();

            if($category) {
                $item->categories()->attach($category);
            }
        });
    }

    public function sold()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'sold',
            ];
        });
    }
}
