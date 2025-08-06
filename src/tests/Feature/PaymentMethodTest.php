<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function a_user_can_select_a_payment_method_and_it_is_submitted_correctly()
    {
        // PHPUnitはフロントエンドの挙動を直接検証するのは難しいため、ここでは「選択した支払い方法がフォーム送信時に正しく扱われるか」 を検証する
        
        $buyingUser = User::factory()->create();
        $exhibitingUser = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $exhibitingUser->id]);
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

        // ユーザーにログインする
        $this->actingAs($buyingUser);

        // 支払い方法に「コンビニ払い」を選択したと仮定して、フォームを送信
        $response = $this->post(route('purchases.store', ['item' => $item->id]), [
            'payment_method' => 'コンビニ払い',
        ]);

        // フォーム送信後にStripeのチェックアウトページにリダイレクトされることを確認
        $response->assertRedirect('https://checkout.stripe.com/test-session');

        // データベースに支払い方法が正しく保存されていることを確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $buyingUser->id,
            'item_id' => $item->id,
            'payment_method' => 'コンビニ払い',
        ]);
    }
}
