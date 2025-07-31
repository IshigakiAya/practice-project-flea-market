<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// --- 認証不要ルート ---
// トップページ（商品一覧）
Route::get('/', [ItemController::class, 'index'])->name('items.index');
// 商品詳細ページ
Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');

// --- 認証が必要なルート---
Route::middleware(['auth', 'verified'])->group(function() {
    Route::get('/dashboard', function() {
        return view('dashboard');
    });

    // 商品関連
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create'); // 商品出品フォーム
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store'); // 商品出品処理

    // いいね機能
    Route::post('/items/{item}/like', [LikeController::class, 'store'])->name('likes.store'); // いいね追加
    Route::delete('/items/{item}/like', [LikeController::class, 'destroy'])->name('likes.destroy'); // いいね削除

    // コメント機能
    Route::post('/items/{item}/comment', [CommentController::class, 'store'])->name('comments.store'); // コメント投稿

    // 購入関連
    Route::get('/purchase/{item}', [PurchaseController::class, 'create'])->name('purchases.create'); // 購入画面
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchases.store'); // 購入処理実行
    // stripe導入
    Route::get('/purchase/{item}/success', [PurchaseController::class, 'success'])->name('purchases.success');
    Route::get('/purchase/{item}/cancel',  [PurchaseController::class, 'cancel'])->name('purchases.cancel');

    // 住所変更（購入手続き内）
    Route::get('/purchase/{item}/address/edit', [AddressController::class, 'edit'])->name('addresses.edit'); // 住所編集フォーム
    Route::patch('/purchase/{item}/address', [AddressController::class, 'update'])->name('addresses.update'); // 住所更新処理

    // マイページ関連
    Route::get('/mypage', [UserController::class, 'show'])->name('users.show'); // マイページ表示
    Route::get('/mypage/profile', [UserController::class, 'edit'])->name('users.edit'); // プロフィール編集画面
    Route::patch('/mypage/profile', [UserController::class, 'update'])->name('users.update'); // プロフィール更新処理
});