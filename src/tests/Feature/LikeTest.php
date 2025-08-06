<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_logged_in_user_can_like_an_item_and_the_like_count_increases()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // ユーザーにログイン
        $this->actingAs($user);

        // 商品詳細ページを開く
        $response = $this->get(route('items.show', $item->id));

        // いいねアイコンを押下
        $response = $this->post(route('likes.store', $item->id));

        $response->assertRedirect();

        // データベースにいいねが登録されていることを確認
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // いいね後の商品詳細ページに再度アクセス
        $response = $this->get(route('items.show', $item->id));

        // いいね数が1として表示されることを確認
        $response->assertSee('<p class="count-number">1</p>', false);

    }
    /** @test */
    public function a_liked_item_displays_a_solid_star_icon_on_the_detail_page()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // ユーザーにログイン
        $this->actingAs($user);

        // 商品詳細ページを開く
        $this->get(route('items.show', $item->id));

        // いいねアイコンを押下
        $response = $this->post(route('likes.store', $item->id));

        // リダイレクトを確認
        $response->assertRedirect();

        // ユーザーモデルをリロードして、最新のlikesリレーションシップを取得
        $user->refresh();

        // いいね後の商品詳細ページに再度アクセス
        $response = $this->get(route('items.show', $item->id));

        // 変更されたアイコン（fa-solid）が表示されていることを確認
        $response->assertSee('<i class="fa-solid fa-star"></i>', false);

        // 元のアイコン（fa-regular）が表示されていないことを確認
        $response->assertDontSee('<i class="fa-regular fa-star"></i>', false);
    }
    /** @test */
    public function a_logged_in_user_can_unlike_an_item_and_the_like_count_decreases()
    {
        // いいね済みの状態を作成する
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $item->likedByUsers()->attach($user);

        // ユーザーにログイン
        $this->actingAs($user);

        // 商品詳細ページを開く（この時点でいいね数は1）
        $this->get(route('items.show', $item->id));

        // いいねアイコンを再度押下して、いいねを解除する
        $response = $this->post(route('likes.store', $item->id));

        // リダイレクトを確認
        $response->assertRedirect();

        // ユーザーモデルをリロードして、最新のlikesリレーションシップを取得
        $user->refresh();

        // データベースからいいねが削除されていることを確認
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // いいね解除後の商品詳細ページに再度アクセス
        $response = $this->get(route('items.show', $item->id));

        // いいね数が0として表示されることを確認
        $response->assertSee('<p class="count-number">0</p>', false);

        // アイコンが元の状態（fa-regular）に戻っていることを確認
        $response->assertSee('<i class="fa-regular fa-star"></i>', false);
        $response->assertDontSee('<i class="fa-solid fa-star"></i>', false);
    }
}
