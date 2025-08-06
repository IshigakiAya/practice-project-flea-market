<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function all_items_are_displayed_on_the_items_page()
    {
        $items = Item::factory()->count(10)->create();

        // 商品ページを開く
        $response = $this->get('/');

        $response->assertStatus(200);

        // レスポンスにデータベースから取得したすべての商品が含まれていることを確認
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }

        // データベースに作成した商品数が正しく表示されているかを確認
        $response->assertViewHas('items', function ($viewItems) use ($items) {
            return count($viewItems) === count($items);
        });
    }

    /**
     * @test
     */
    public function sold_label_is_displayed_on_purchased_items()
    {
        // 購入済商品・未購入商品を作成
        $purchasedItem = Item::factory()->create(['status' => 'active']);
        $unpurchasedItem = Item::factory()->create(['status' => 'active']);

        // 購入済商品のstatusをsoldに更新
        $purchasedItem->status = 'sold';
        $purchasedItem->save();

        // 商品ページを開く
        $response = $this->get('/');

        $response->assertStatus(200);

        // 購入済商品に"Sold"ラベルが表示される
        $response->assertSeeTextInOrder([$purchasedItem->name, 'Sold']);

        // レスポンス内の "Sold" という文字列の出現回数を検証(未購入商品に"Sold"表示がないことを確認)
        $responseContent = $response->getContent();
        $this->assertEquals(1, substr_count($responseContent, 'Sold'));
    }

    /**
     * @test
     */
    public function user_cannot_see_their_own_items_in_the_list()
    {
        // ログインユーザーを作成
        $loggedInUser = User::factory()->create();

        // ログインユーザーが出品した商品をItemFactoryで作成
        $ownItem = Item::factory()->create([
            'user_id' => $loggedInUser->id,
            'name' => '自分の出品商品',
        ]);

        // 他のユーザーが出品した商品をItemFactoryで作成
        $otherUser = User::factory()->create();
        $otherItem = Item::factory()->create([
            'user_id' => $otherUser->id,
            'name' => '他人の出品商品',
        ]);

        // ログインした状態で商品ページを開く
        $response = $this->actingAs($loggedInUser)->get('/');

        $response->assertStatus(200);

        // 自分の出品が表示されないことを検証
        $response->assertDontSeeText($ownItem->name);

        // 他人の出品が表示されることを検証
        $response->assertSeeText($otherItem->name);
    }
}