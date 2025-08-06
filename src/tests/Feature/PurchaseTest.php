<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_logged_in_user_can_purchase_an_item_successfully()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        // Stripe\StripeクラスとStripe\Checkout\Sessionクラスをモック化
        // Stripe::setApiKeyをモック化
        Mockery::mock('alias:\Stripe\Stripe')
            ->shouldReceive('setApiKey')
            ->once()
            ->with(config('services.stripe.secret'))
            ->andReturn(null);

        // Stripe Checkout Sessionの作成をモック化
        Mockery::mock('alias:\Stripe\Checkout\Session')
            ->shouldReceive('create')
            ->once()
            ->andReturn((object)['url' => 'https://checkout.stripe.com/test-session']);

        // ユーザーにログインする
        $this->actingAs($user);

        // 商品購入画面を開く
        $this->get(route('purchases.create', $item->id));

        // 商品を選択して「購入する」ボタンを押下
        $response = $this->post(route('purchases.store', ['item' => $item->id]), [
            'payment_method' => 'カード支払い',
        ]);

        // Stripeのチェックアウトページにリダイレクトされることを確認
        $response->assertRedirect('https://checkout.stripe.com/test-session');

        // データベースに購入履歴が保存されていることを確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => 'カード支払い',
        ]);

        // 商品のステータスが'sold'に更新されていることを確認
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'status' => 'sold',
        ]);
    }

    /** @test */
    public function a_purchased_item_is_displayed_as_sold_on_the_index_page()
    {
        $buyingUser = User::factory()->create(); // 購入するユーザー
        $exhibitingUser = User::factory()->create(); // 商品を出品するユーザー

        $item = Item::factory()->create(['user_id' => $exhibitingUser->id, 'name' => '購入した商品']);
        Address::factory()->create(['user_id' => $buyingUser->id]);

        // Stripe API呼び出しをモック化
        Mockery::mock('alias:\Stripe\Stripe')
            ->shouldReceive('setApiKey')
            ->once()
            ->andReturn(null);

        Mockery::mock('alias:\Stripe\Checkout\Session')
            ->shouldReceive('create')
            ->once()
            ->andReturn((object)['url' => 'https://checkout.stripe.com/test-session']);

        // 購入するユーザーとしてログインする
        $this->actingAs($buyingUser);

        // 商品購入画面を開く
        $this->get(route('purchases.create', $item->id));

        // 商品を選択して「購入する」ボタンを押下
        $this->post(route('purchases.store', ['item' => $item->id]), [
            'payment_method' => 'カード支払い',
        ]);

        // 購入処理が完了したことを確認（データベースを直接検証）
        $this->assertDatabaseHas('items', ['id' => $item->id, 'status' => 'sold']);

        // 商品一覧画面を表示する
        $response = $this->get(route('items.index'));

        $response->assertStatus(200);

        // 購入した商品が「Sold」と表示されていることを確認
        $response->assertSeeTextInOrder([$item->name, 'Sold']);
    }

    /** @test */
    public function a_purchased_item_is_added_to_the_users_purchase_list()
    {
        $buyingUser = User::factory()->create(); // 購入するユーザー
        $exhibitingUser = User::factory()->create(); // 商品を出品するユーザー

        $item = Item::factory()->create([
            'user_id' => $exhibitingUser->id, 'name' => '購入した商品',
        ]);
        Address::factory()->create(['user_id' => $buyingUser->id]);

        // Stripe API呼び出しをモック化
        Mockery::mock('alias:\Stripe\Stripe')
            ->shouldReceive('setApiKey')
            ->once()
            ->with(config('services.stripe.secret'))
            ->andReturn(null);

        Mockery::mock('alias:\Stripe\Checkout\Session')
            ->shouldReceive('create')
            ->once()
            ->andReturn((object)['url' => 'https://checkout.stripe.com/test-session']);

        // 購入するユーザーとしてログイン
        $this->actingAs($buyingUser);

        // 商品購入画面を開く
        $this->get(route('purchases.create', $item->id));

        // 「購入する」ボタンを押下
        $this->post(route('purchases.store', ['item' => $item->id]), [
            'payment_method' => 'カード支払い',
        ]);

        // データベースでPurchaseが作成されたことを確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $buyingUser->id,
            'item_id' => $item->id,
        ]);

        // プロフィール画面を表示する（購入タブを指定）
        $response = $this->get(route('users.show', ['tab' => 'buy']));

        $response->assertStatus(200);

        // 購入した商品がプロフィールの購入一覧に表示されていることを確認
        $response->assertSeeText($item->name);

    }
}
