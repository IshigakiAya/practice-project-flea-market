<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MylistTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function only_liked_items_are_displayed_in_mylist()
    {
        $likingUser = User::factory()->create(); // いいねをするユーザー
        $exhibitingUser = User::factory()->create(); // 商品を出品するユーザー

        $likedItem = Item::factory()->create(['user_id' => $exhibitingUser->id, 'name' => 'いいねした商品']);
        $unlikedItem = Item::factory()->create(['name' => 'いいねしていない商品']);

        $likedItem->likedByUsers()->attach($likingUser);

        // いいねをするユーザーとしてログイン
        $this->actingAs($likingUser);

        // マイリストタブを指定して商品一覧ページにアクセス
        $response = $this->get(route('items.index', ['tab' => 'mylist']));

        $response->assertStatus(200);

        // いいねした商品（他人の出品）が表示されていることを確認
        $response->assertSeeText($likedItem->name);

        // いいねしていない商品が表示されていないことを確認
        $response->assertDontSeeText($unlikedItem->name);

        // 自分が出品した商品にいいねした場合のケースも追加
        $selfExhibitedItem = Item::factory()->create(['user_id' => $likingUser->id, 'name' => '自分の出品商品']);
        $selfExhibitedItem->likedByUsers()->attach($likingUser);

        $response = $this->get(route('items.index', ['tab' => 'mylist']));
        $response->assertDontSeeText($selfExhibitedItem->name);
    }
    /**
     * @test
     */
    public function sold_label_is_displayed_on_purchased_items_in_mylist()
    {
        $loggedInUser = User::factory()->create();
        $otherUser = User::factory()->create();

        // ログインユーザーがいいねした、購入済の商品を作成
        $soldItem = Item::factory()->sold()->create(['user_id' => $otherUser->id]);
        $soldItem->likedByUsers()->attach($loggedInUser);

        // ログインユーザーがいいねした、未購入の商品を作成
        $activeItem = Item::factory()->create(['user_id' => $otherUser->id]);
        $activeItem->likedByUsers()->attach($loggedInUser);

        // ログインした状態でマイリストページを開く
        $response = $this->actingAs($loggedInUser)->get('/?tab=mylist');

        $response->assertStatus(200);

        // 購入済の商品に「Sold」ラベルが表示される
        $response->assertSeeTextInOrder([$soldItem->name, 'Sold']);

        // 「Sold」表示は１つ（未購入の商品には表示されないことを検証）
        $responseContent = $response->getContent();$this->assertEquals(1, substr_count($responseContent, 'Sold'));
    }
    /**
     * @test
     */
    public function own_items_are_not_displayed_in_mylist()
    {
        $user = User::factory()->create();
        // 自分が出品した商品を作成
        $ownItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => '自分の出品商品'
        ]);

        // その商品にいいねする
        $ownItem->likedByUsers()->attach($user);

        // ユーザーにログインする
        $this->actingAs($user);

        // マイリストページを開く
        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);

        // 自分が出品した商品が表示されない
        $response->assertDontSeeText($ownItem->name);
    }
    /**
     * @test
     */
    public function no_items_are_displayed_for_unautenticated_users_on_mylist_page()
    {
        $item = Item::factory()->create();

        // 未認証の状態でマイリストページを開く
        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);

        // ビューに渡されるitems変数が空であることを確認
        $response->assertViewHas('items', function($items) {
            return $items->isEmpty();
        });

        $response->assertDontSeeText($item->name);
    }
}
