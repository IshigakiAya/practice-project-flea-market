<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        // ユーザーにログインする
        $this->actingAs($user);

        // ログアウトボタンを押す
        $response = $this->post('/logout');

        $response->assertRedirect('/');

        // ログアウト処理が実行されたことを確認
        $this->assertGuest();
    }
}
