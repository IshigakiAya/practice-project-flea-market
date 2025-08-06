<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_registered_address_is_reflected_on_the_purchase_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $originalAddress = Address::factory()->create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区',
            'building' => 'コーポ101',
        ]);

        $newAddressData = [
            'postal_code' => '000-0000',
            'address' => '大阪府大阪市北区',
            'building' => 'ビルディング202',
        ];

        // ユーザーにログインする
        $this->actingAs($user);

        // 送付先住所変更画面で住所を登録する
        $response = $this->patch(route('addresses.update', $item->id), $newAddressData);

        // 更新後、商品購入画面へリダイレクトされることを確認
        $response->assertRedirect(route('purchases.create', $item->id));

        // データベースに新しい住所が保存されたことを確認
        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'postal_code' => $newAddressData['postal_code'],
            'address' => $newAddressData['address'],
            'building' => $newAddressData['building'],
        ]);

        // 商品購入画面を再度開く
        $response = $this->get(route('purchases.create', $item->id));

        $response->assertStatus(200);

        // 登録した新しい住所が商品購入画面に正しく反映されていることを確認
        $response->assertSeeText('〒 ' . $newAddressData['postal_code']);
        $response->assertSeeText($newAddressData['address']);
        $response->assertSeeText($newAddressData['building']);
    }

    /** @test */
    public function a_purchased_item_is_registered_with_the_correct_delivery_address()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $newAddressData = [
            'postal_code' => '000-0000',
            'address' => '大阪府大阪市北区',
            'building' => 'ビルディング202',
        ];

        // ユーザーにログインする
        $this->actingAs($user);

        // 送付先住所変更画面で住所を登録する
        // AddressControllerのupdateメソッドは、既にaddressレコードが存在していることを前提としているため、事前にファクトリで住所を作成
        Address::factory()->create(['user_id' => $user->id]);
        $this->patch(route('addresses.update', $item->id), $newAddressData);

        // 商品を購入する
        // Stripe APIのモック化
        Mockery::mock('alias:\Stripe\Stripe')
            ->shouldReceive('setApiKey')
            ->once()
            ->with(config('services.stripe.secret'))
            ->andReturn(null);

        Mockery::mock('alias:\Stripe\Checkout\Session')
            ->shouldReceive('create')
            ->once()
            ->andReturn((object)['url' => 'https://checkout.stripe.com/test-session']);

        // 購入処理を実行
        $this->post(route('purchases.store', ['item' => $item->id]),[
            'payment_method' => 'カード支払い',
        ]);

        // 購入後の最新のAddressモデルを取得
        $latestAddress = $user->address()->first();

        // 正しく送付先住所が紐づいていることを確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'address_id' => $latestAddress->id,
        ]);
    }
}
