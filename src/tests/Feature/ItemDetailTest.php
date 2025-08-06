<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function all_required_information_is_displayed_on_the_item_detail_page()
    {
        // テストデータの準備
        // テストごとに完全に独立したデータを作成する
        $user = User::factory()->create(['name' => 'コメントユーザー']);
        $otherUser = User::factory()->create(['name' => '出品者']);

        $item = Item::factory()->create([
            'user_id' => $otherUser->id,
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 12345,
            'description' => 'これはテスト用の商品説明です。',
            'condition' => '良好',
            'image' => 'items/test_image.jpg',
        ]);

        // カテゴリを複数紐付け
        $category1 = Category::factory()->create(['name' => 'インテリア']);
        $category2 = Category::factory()->create(['name' => 'ゲーム']);
        $item->categories()->attach([$category1->id, $category2->id]);

        // いいねとコメントを付与
        $item->likedByUsers()->attach($user);
        $comment = $item->comments()->create([
            'user_id' => $user->id,
            'comment' => 'これはテスト用のコメントです。',
        ]);

        // 商品詳細ページを開く
        $response = $this->get("/items/{$item->id}");

        $response->assertStatus(200);

        // 商品画像
        $response->assertSee('storage/' . $item->image);

        // 商品情報
        $response->assertSeeText($item->name);
        $response->assertSeeText($item->brand);
        $response->assertSeeText('¥' . number_format($item->price) . '(税込)');
        $response->assertSeeText($item->description);
        $response->assertSeeText($item->condition);

        // 複数カテゴリを検証
        $response->assertSeeText($category1->name);
        $response->assertSeeText($category2->name);

        // いいねとコメント数
        $response->assertSeeText($item->likedByUsers()->count());
        $response->assertSeeText($item->comments()->count());

        // コメント情報
        $response->assertSeeText($comment->user->name);
        $response->assertSeeText($comment->comment);
    }
    /**
     * @test
     */
    public function multiple_selected_categories_are_displayed_on_the_item_detail_page()
    {
        // テストデータの作成
        $user = User::factory()->create();
        $categories = Category::factory()->count(2)->create();
        $item = Item::factory()->create(['user_id' => $user->id]);
        $item->categories()->sync($categories);

        // 商品詳細ページを開く
        $response = $this->get("/items/{$item->id}");

        $response->assertStatus(200);
        // 複数選択されたカテゴリが表示されている
        foreach($categories as $category) {
            $response->assertSeeText($category->name);
        }
    }
}
