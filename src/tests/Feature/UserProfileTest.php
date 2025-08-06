<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_user_profile_page_displays_correct_information_and_items()
    {
        Storage::fake('public');

        // ユーザー情報を作成
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => UploadedFile::fake()->create('profile.txt', 10, 'text/plain')->store('profile_images', 'public'),
        ]);

        // 出品した商品を作成
        $exhibitedItem = Item::factory()->create([
            'user_id' => $user->id,
            'name' => 'ユーザーが出品したユニークな商品名_XYZ_123',
        ]);

        // 購入した商品を作成
        $anotherUser = User::factory()->create();
        $purchasedItem = Item::factory()->create([
            'user_id' => $anotherUser->id,
            'name' => 'ユーザーが購入したユニークな商品名_ABC_456',
        ]);

        // 購入レコードを作成
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $purchasedItem->id,
            'address_id' => \App\Models\Address::factory()->create(['user_id' => $user->id])->id,
            'payment_method' => 'カード支払い',
        ]);

        // ユーザーにロクインする
        $this->actingAs($user);

        // プロフィールページを開く
        $response = $this->get(route('users.show', ['tab' => 'sell']));
        $response->assertStatus(200);

        // ユーザー情報が正しく表示されていることを確認
        $response->assertSeeText($user->name);
        $response->assertSee(asset('storage/' . $user->profile_image));

        // 出品タブの内容が正しく表示されていることを確認
        $response->assertSeeText($exhibitedItem->name);
        $response->assertDontSeeText($purchasedItem->name);

        // 「購入した商品」タブに切り替えて、購入商品が表示されることを確認
        $response = $this->get(route('users.show', ['tab' => 'buy']));
        $response->assertStatus(200);

        // ユーザー名と購入した商品名が表示されていることを確認
        $response->assertSeeText($user->name);
        $response->assertSeeText($purchasedItem->name);
        $response->assertDontSeeText($exhibitedItem->name);
    }
}
