<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function partial_match_search_works_by_item_name()
    {
        // 検索にヒットする商品を作成
        $targetItem = Item::factory()->create(['name' => 'テスト用ノートPC']);

        // 検索にヒットしない商品を作成
        $otherItem = Item::factory()->create(['name' => 'テスト用スマートフォン']);

        // 検索欄にキーワードを入力
        $keyword = 'ノートPC';

        // 検索ボタンを押す
        $response = $this->get('/?keyword=' . $keyword);

        $response->assertStatus(200);

        // 検索にヒットした商品が表示される
        $response->assertSeeText($targetItem->name);

        // 検索にヒットしない商品が表示されない
        $response->assertDontSeeText($otherItem->name);
    }
    /**
     * @test
     */
    public function search_keyword_is_retained_when_switcing_to_mylist_tab()
    {
        $loggedInUser = User::factory()->create();
        $otherUser = User::factory()->create();
        $keyword = 'カメラ';

        // 検索にヒットする商品を作成
        $targetItem = Item::factory()->create([
            'name' => 'デジタルカメラ',
            'user_id' => $otherUser->id
        ]);
        $targetItem->likedByUsers()->attach($loggedInUser);

        // 検索にヒットしない商品を作成
        $otherItem = Item::factory()->create([
            'name' => 'スマートフォン',
            'user_id' => $otherUser->id
        ]);
        $otherItem->likedByUsers()->attach($loggedInUser);

        // 検索を実行し、結果を検証
        $response = $this->actingAs($loggedInUser)->get("/?keyword={$keyword}");

        $response->assertStatus(200);
        $response->assertSeeText($targetItem->name);
        $response->assertDontSeeText($otherItem->name);

        // マイリストページに遷移し、キーワードが保持されているか検証
        $response = $this->actingAs($loggedInUser)->get("/?keyword={$keyword}&tab=mylist");

        $response->assertStatus(200);
        $response->assertSeeText($targetItem->name);
        $response->assertDontSeeText($otherItem->name);
    }
}
