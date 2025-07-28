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

        $paymentTypes = ($paymentMethod === 'konbini') ? ['konbini'] : ['card'];

        // Stripe Checkout セッション作成
        $session = CheckoutSession::create([
            'payment_method_types' => $paymentTypes,
            'line_items' =>[[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'customer_email' => Auth::user()->email,
            'success_url' => route('purchases.success', ['item' => $item->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('purchases.cancel', ['item' => $item->id]),
            ]);

        return redirect()->away($session->url);
    }

    public function success(Request $request, Item $item) {
        // Stripe決済の成功確認処理
        $sessionId = $request->get('session_id');
        Stripe::setApiKey(config('services.stripe.secret'));
        $session = \Stripe\Checkout\Session::retrieve($sessionId);

        if ($session->payment_status === 'paid') {
            // Purchase登録
            Purchase::create([
                'user_id' => Auth::id(),
                'item_id' => $item->id,
                'address_id' => Auth::user()->address->id,
                'payment_method' => $session->payment_method_types[0] ?? 'card',
            ]);

            // 商品をsoldに更新
            $item->status = 'sold';
            $item->save();

            return redirect()->route('items.index')->with('success', '商品を購入しました');
        }

        return redirect()->route('purchases.create', $item)->withErrors('決済が完了していません');
    }

    public function cancel(Item $item) {
        return redirect()
            ->route('purchases.create', $item)
            ->with('error', '決済がキャンセルされました。');
    }
}
