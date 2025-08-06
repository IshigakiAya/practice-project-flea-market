<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_new_item()
    {
        Storage::fake('public');

        // ユーザーにログインする
        $user = User::factory()->create();
        $this->actingAs($user);

        // カテゴリーを作成
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        // 適切な情報でフォームを送信
        $imageFile = UploadedFile::fake()->create('test_image.txt', 10, 'image/jpeg');
        $itemData = [
            'name' => 'テスト商品名',
            'brand' => 'テストブランド',
            'price' => 1500,
            'categories' => [$category1->id, $category2->id],
            'condition' => '良好',
            'description' => 'これはテスト用の商品の説明です。',
            'image' => $imageFile,
        ];

        // 商品出品画面にアクセスし、POSTリクエストを送信
        $response = $this->post(route('items.store'), $itemData);

        // マイページにリダイレクトされる
        $response->assertRedirect(route('users.show'));

        // データベースに商品情報が正しく保存されている
        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'name' => 'テスト商品名',
            'condition' => '良好',
            'brand' => 'テストブランド',
            'description' => 'これはテスト用の商品の説明です。',
            'price' => 1500,
        ]);

        // 保存された商品のIDを取得する
        $item = Item::where('user_id', $user->id)
        ->where('name', 'テスト商品名')
        ->firstOrFail();

        // 商品画像がストレージに保存されている
        Storage::disk('public')->assertExists('items/' . $imageFile->hashName());

        // 中間テーブルにカテゴリーが正しく保存されている
        $this->assertDatabaseHas('category_item', [
            'item_id' => $item->id,
            'category_id' => $category1->id,
        ]);
        $this->assertDatabaseHas('category_item', [
            'item_id' => $item->id,
            'category_id' => $category2->id,
        ]);
    }
}
