<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(CommentRequest $request) {
        $comment = new Comment([
            'user_id' => auth()->id(),
            'item_id' => $request->input('item_id'),
            'comment' => $request->input('comment'),
        ]);

        $comment->save();
        return redirect()->back();
    }

    public function construct() {
        $this->middleware('auth');
        // 未認証のユーザーは自動的にログイン画面にリダイレクト
    }
}
