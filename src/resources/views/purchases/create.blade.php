@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchases/create.css') }}">
@endsection

@section('content')
<div class="purchase-page-container">
    <form class="form" action="{{ route('purchases.store', ['item' => $item->id]) }}" method="POST" novalidate>
        @csrf
        <div class="purchase-page__column--left">
            <div class="product-overview">
                <div class="product-overview__image-wrapper">
                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="product-overview__image"/>
                </div>
                <div class="product-overview__details">
                    <h1 class="product-overview__name">{{ $item->name }}</h1>
                    <p class="product-overview__price">
                        <span class="yen-symbol">&#165</span>
                        {{ number_format($item->price) }}
                    </p>
                </div>
            </div>

            <div class="payment-method-section">
                <h2 class="section-title">支払い方法</h2>
                <div class="form-group">
                    <select class="form__select" name="payment_method" required>
                        <option value="" disabled selected>選択してください</option>
                        <option value="コンビニ払い">コンビニ払い</option>
                        <option value="カード支払い">カード支払い</option>
                    </select>
                    <div class="form__error">
                        @error('payment_method')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>

            <div class="delivery-address-section">
                <div class="section-header">
                    <h2 class="section-title">配送先</h2>
                    <a href="{{ route('addresses.edit',['item' => $item->id]) }}" class="edit-address">変更する</a>
                </div>
                <div class="address-display">
                    <p>〒 {{ $deliveryAddress->postal_code ?? '未登録' }}</p>
                    <p>{{ $deliveryAddress->address ?? '未登録 '}}</p>
                    @if($deliveryAddress->building)
                        <p>{{ $deliveryAddress->building }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="purchase-page__column--right">
            <table class="order-summary-table">
                <tr class="summary-row">
                    <th class="summary-label">商品代金</th>
                    <td class="summary-value">
                        <span class="yen-symbol">&#165</span>
                        {{ number_format($item->price) }}
                    </td>
                </tr>
                <tr class="summary-row">
                    <th class="summary-label">支払い方法</th>
                    <td class="summary-value">
                        <span id="selected-payment-method"></span>
                        {{-- JavaScriptにより、選択した支払い方法を更新 --}}
                    </td>
                </tr>
            </table>
            <div class="form__button">
                <button class="form__button-submit" type="submit">購入する</button>
            </div>
        </div>
    </form>
</div>

<script>
    // 支払い方法の選択肢に応じて表示を変える
    const select = document.querySelector('.form__select');
    const display = document.getElementById('selected-payment-method');

    if(select && display) {
        select.addEventListener('change', function() {
            display.textContent = this.value;
        });
        // 初期値表示
        display.textContent = select.value;
    }
</script>
@endsection
