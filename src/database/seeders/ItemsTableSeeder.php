<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon; // created_at, updated_at 用にCarbonをインポート

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userId = User::all()->random()->id;

        $categories = Category::all();

        $rawItems = [
            [
                'name' => '腕時計',
                'brand' => 'EMPORIO ARMANI',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'condition' => '良好',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'category_names' => ['ファッション', 'メンズ'],
            ],
            [
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'condition' => '目立った傷や汚れなし',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'category_names' => ['家電'],
            ],
            [
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'condition' => 'やや傷や汚れあり',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'category_names' => ['キッチン'],
            ],
            [
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'condition' => '状態が悪い',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'category_names' => ['ファッション', 'メンズ'],
            ],
            [
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'condition' => '良好',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'category_names' => ['家電'],
            ],
            [
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'condition' => '目立った傷や汚れなし',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'category_names' => ['家電'],
            ],
            [
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'condition' => 'やや傷や汚れあり',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'category_names' => ['ファッション', 'レディース'],
            ],
            [
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'condition' => '状態が悪い',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'category_names' => ['キッチン'],
            ],
            [
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'condition' => '良好',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'category_names' => ['インテリア','キッチン'],
            ],
            [
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'condition' => '目立った傷や汚れなし',
                'image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'category_names' => ['コスメ'],
            ],
        ];

        foreach ($rawItems as $item) {
            // 画像保存処理
            $imageContent = Http::get($item['image_url'])->body();
            $filename = 'items/' . Str::uuid() . '.jpg';
            Storage::disk('public')->put($filename, $imageContent);

            // 商品の作成
            $newItem = Item::create([
                'user_id' => $userId,
                'name' => $item['name'],
                'price' => $item['price'],
                'description' => $item['description'],
                'condition' => $item['condition'],
                'image' => $filename,
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // カテゴリ名を元に実際のカテゴリIDを取得し、紐付け
            if (isset($item['category_names']) && is_array($item['category_names'])) {
                $itemCategories = $categories->filter(function($category) use ($item) {
                    return in_array($category->name, $item['category_names']);
                })->pluck('id'); // IDのみを抽出

                // 取得したIDを中間テーブルに紐付け
                $newItem->categories()->attach($itemCategories);
            }
        }
    }
}
