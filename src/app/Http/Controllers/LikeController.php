<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class LikeController extends Controller
{
    public function store(Item $item) {
        $user = auth()->user();
        if ($user->likes()->where('item_id', $item->id)->exists()) {
            // すでにいいねしている → 解除
            $user->likes()->detach($item->id);
        } else {
            // いいねしていない → 付与
            $user->likes()->attach($item->id);
        }
        return back();
    }

    public function construct() {
        $this->middleware('auth');
    } // 未認証のユーザーは自動的にログイン画面にリダイレクト
}