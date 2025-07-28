<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
use App\Models\User;
use App\Models\Address;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function edit()
    {
        $user = auth()->user();

        $address = $user->address;

        return view('users.edit', compact('user','address'));
    }

    public function update(ProfileRequest $request)
    {
        $user = auth()->user();
        $user->name = $request->name;

        // プロフィール画像の処理
        if($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $path = $image->store('profile_images', 'public');
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $user->profile_image = $path;
        }

        $user->save();

        if ($user->address) {
            // 既存の住所を更新
            $user->address->update([
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $request->building,
            ]);
        } else {
            $user->address()->create([
            // 住所が未登録の場合、新規作成
                'postal_code' => $request->postal_code,
                'address' => $request->address,
                'building' => $request->building,
            ]);
        }

        return redirect('/?tab=mylist');
    }

    public function show(Request $request)
    {
        $user = auth()->user();
        $tab = $request->query('tab', 'sell');
        $items = collect();
        $purchases = collect();

        if ($tab === 'sell') {
            $items = $user->items()->latest()->get();
        } elseif ($tab === 'buy') {
            $purchases = $user->purchases()->with('item')->latest()->get();
        }

        return view('users.show', compact('user', 'tab', 'items', 'purchases'));
    }
}
