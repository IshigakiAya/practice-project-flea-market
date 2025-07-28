@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/addresses/edit.css') }}">
@endsection

@section('content')
<div class="form__wrapper">
    <div class="form__heading">
        <h1>住所の変更</h1>
    </div>
    <form class="form" action="{{ route('addresses.update', ['item' => $item->id]) }}" method="POST">
        @csrf
        @method('PATCH')
        {{-- 郵便番号 --}}
        <div class="form__group">
            <div class="form__group-title">
                <label class="form__label--item">郵便番号</label>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="postal_code" value="{{ $deliveryAddress->postal_code ?? '' }}" />
                    {{-- $postal_codeが存在すればその値を、なければ空文字を表示 --}}
                </div>
                <div class="form__error">
                    @error('postal_code')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        {{-- 住所 --}}
        <div class="form__group">
            <div class="form__group-title">
                <label class="form__label--item">住所</label>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="address" value="{{ $deliveryAddress->address ?? '' }}" />
                </div>
                <div class="form__error">
                    @error('address')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        {{-- 建物名 --}}
        <div class="form__group">
            <div class="form__group-title">
                <label class="form__label--item">建物名</label>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="building" value="{{ $deliveryAddress->building ?? '' }}" />
                </div>
                <div class="form__error">
                    @error('building')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__button">
            <button class="form__button-submit" type="submit">更新する</button>
        </div>
    </form>
</div>
@endsection
