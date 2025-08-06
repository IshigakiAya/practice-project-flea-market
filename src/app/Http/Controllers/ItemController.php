<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request) {
        $query = Item::withCount('likedByUsers');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        if ($request->tab === 'mylist') {
            if (Auth::check()) {
                // いいねした商品のみを表示
                $query->whereHas('likedByUsers', function($q){
                    $q->where('user_id', Auth::id());
                })
                ->where('user_id', '!=', Auth::id());
            } else {
                // 未ログインの場合
                $items = collect();
                return view('items.index', compact('items'));
            }
        } else {
            // タブ指定なしのとき
            if (Auth::check()) {
                // 自分の出品を除外
                $query->where('user_id', '!=', Auth::id());
            }
        }
        $items = $query->get();
        return view('items.index', compact('items'));
    }


    public function show(Item $item) {
        $item = Item::with([
            'likedByUsers',
            'comments.user',// コメント・投稿したユーザー
            'categories',
            'user'
        ])
        ->findOrFail($item->id);

        $likesCount = $item->LikedByUsers->count();
        $commentsCount = $item->comments->count();

        return view('items.show',compact('item', 'likesCount','commentsCount'));
    }


    public function create() {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }


    public function store(ExhibitionRequest $request) {
        // 出品処理
        $item = new Item();
        $item->user_id = Auth::id();
        $item->name = $request->name;
        $item->brand = $request->brand;
        $item->price = $request->price;
        $item->description = $request->description;
        $item->condition = $request->condition;
        $item->status = $request->status ?? 'active';

        if($request->hasFile('image')) {
            $item->image = $request->file('image')->store('items', 'public');
        } else {
            $item->image = null;
        }

        $item->save();
        if($request->has('categories')) {
            $item->categories()->attach($request->categories);
        }
        return redirect()->route('users.show');
    }
}