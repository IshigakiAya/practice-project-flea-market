<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_logged_in_user_can_submit_a_comment_and_the_comment_count_increases()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $commentText = 'これはテストコメントです。';

        // ユーザーにログインする
        $this->actingAs($user);

        // コメントを入力する
        // コメントボタンを押す
        $response = $this->post(route('comments.store', ['item' => $item->id]), [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => $commentText,
        ]);

        // コメント送信後にリダイレクトされることを確認
        $response->assertRedirect();

        // データベースにコメントが保存されていることを確認
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => $commentText,
        ]);

        // コメント後の商品詳細ページに再度アクセス
        $response = $this->get(route('items.show', $item->id));

        // コメント数が1として表示されることを確認
        $response->assertSeeText('コメント(1)');
        $response->assertSee('<p class="count-number">1</p>', false);

        // 送信したコメントの内容が表示されていることを確認
        $response->assertSeeText($commentText);
    }

    /** @test */
    public function a_guest_cannot_submit_a_comment()
    {
        $item = Item::factory()->create();
        $commentText = 'ゲストからのコメントです。';

        // コメントを入力する
        // コメントボタンを押す
        $response = $this->post(route('comments.store', ['item' => $item->id]), [
            'item_id' => $item->id,
            'comment' => $commentText,
        ]);

        // ログインページへリダイレクトされることを確認
        $response->assertRedirect('/login');

        // データベースにコメントが保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'comment' => $commentText,
        ]);
    }

    /** @test */
    public function a_validation_message_is_displayed_when_comment_is_empty()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // ユーザーにログインする
        $this->actingAs($user);

        // コメントボタンを押す（コメント内容は空）
        $response = $this->post(route('comments.store', ['item' => $item->id]), [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => '',
        ]);

        // バリデーションエラーでリダイレクトされることを確認
        $response->assertSessionHasErrors('comment');
        $response->assertRedirect();

        // リダイレクト後のページ（商品詳細ページ）にアクセス
        $response = $this->get(route('items.show', $item->id));

        // バリデーションメッセージが表示されていることを確認
        $response->assertSeeText('コメントを入力してください');
    }

    /** @test */
    public function a_validation_message_is_displayed_when_comment_is_too_long()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        // 256文字のコメントを作成
        $longComment = str_repeat('a', 256);

        // ユーザーにログインする
        $this->actingAs($user);

        // 256文字以上のコメントを入力する
        // コメントボタンを押す
        $response = $this->post(route('comments.store', ['item' => $item->id]), [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => $longComment,
        ]);

        // バリデーションエラーでリダイレクトされることを確認
        $response->assertSessionHasErrors('comment');
        $response->assertRedirect();

        // リダイレクト後のページ（商品詳細ページ）にアクセス
        $response = $this->get(route('items.show', $item->id));

        // バリデーションメッセージが表示されていることを確認
        $response->assertSeeText('コメントは255文字以内で入力してください');
    }
}
