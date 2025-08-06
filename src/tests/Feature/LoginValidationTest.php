<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_is_required_for_login()
    {
        $response = $this->get('/login');

        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertRedirect();

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    public function test_password_is_required_for_login()
    {
        $response = $this->get('/login');

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertRedirect();

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => 'notexist@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/login');

        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません。'
        ]);
    }

    public function test_login_is_successful_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->get('/login');

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');

        $this->assertAuthenticatedAs($user);

    }
}
