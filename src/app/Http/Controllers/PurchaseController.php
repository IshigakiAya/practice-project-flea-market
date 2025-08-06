<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as CheckoutSession;


class PurchaseController extends Controller
{
    public function create(Item $item) {
        $user = Auth::user();
        $deliveryAddress = $user->address;

        return view('purchases.create', compact('item', 'deliveryAddress'));
    }

    public function store(PurchaseRequest $request, Item $item) {
        $paymentMethod = $request->input('payment_method');

        // Stripe 初期化
        Stripe::setApiKey(config('services.stripe.secret'));

        // Stripe Checkout セッション作成
        $session = CheckoutSession::create([
            'payment_method_types' => ['card', 'konbini'],
            'line_items' =>[[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $item->name],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'customer_email' => Auth::user()->email,
            'success_url' => route('purchases.success', ['item' => $item->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('purchases.cancel', ['item' => $item->id]),
        ]);

        // Webhookを扱わないため購入処理をここで行う（購入履歴登録＋ステータス更新）
        Purchase::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'address_id' => Auth::user()->address->id,
            'payment_method' => $paymentMethod,
        ]);

        $item->status ='sold';
        $item->save();

        return redirect()->away($session->url);
    }

    public function success(Request $request, Item $item) {
        return redirect()->route('items.index');
    }

    public function cancel(Item $item) {
        return redirect()
            ->route('purchases.create', $item);
    }
}
