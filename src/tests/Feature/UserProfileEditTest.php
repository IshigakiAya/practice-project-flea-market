<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfileEditTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function the_profile_edit_page_displays_correct_initial_values()
    {
        Storage::fake('public');

        // テストユーザーと関連データを準備
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'profile_image' => 'profile_images/test_image.jpg',
        ]);
        $address = Address::factory()->create([
            'user_id' => $user->id,
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区千駄ヶ谷1-1-1',
            'building' => 'コーポラス101',
        ]);

        // ダミーテキストファイルを作成して保存（アサーションで検証するため）
        UploadedFile::fake()->create('test_image.txt', 10, 'text/plain')->store('profile_images', 'public');

        // ユーザーとしてログイン
        $this->actingAs($user);

        // プロフィール編集ページにアクセス
        $response = $this->get(route('users.edit'));

        $response->assertStatus(200);

        // フォームの初期値が正しく表示されているかを確認
        // プロフィール画像の検証 (<img>タグのsrc属性をチェック)
        $response->assertSee(asset('storage/' . $user->profile_image));

        // ユーザー名の検証（inputタグのvalue属性をチェック）
        $response->assertSee('value="' . $user->name . '"', false);

        // 郵便番号の検証
        $response->assertSee('value="' . $address->postal_code . '"', false);

        // 住所の検証
        $response->assertSee('value="' . $address->address . '"', false);

        // 建物名の検証
        $response->assertSee('value="' . $address->building . '"', false);
    }
}
