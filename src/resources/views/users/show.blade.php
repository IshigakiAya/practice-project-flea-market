@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/users/show.css') }}">
@endsection

@section('content')
<div class="mypage-container">
    <div class="user-profile">
        <div class="user-profile__image-wrapper">
            @if ($user->profile_image)
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像" class="user-profile__image" />
            @else
                {{-- 画像が未登録の場合、グレーの円を表示 --}}
                <div class="user-profile__placeholder-image"></div>
            @endif
        </div>
        <div class="user-profile__name-wrapper">
            <p class="user-profile__name">{{ $user->name }}</p>
        </div>
        <div class="user-profile__edit">
            <a href="{{ route('users.edit') }}" class="user-profile__edit-link">プロフィールを編集</a>
        </div>
    </div>

    <div class="tabs">
        {{-- 出品タブのリンク --}}
        <a href="{{ route('users.show', ['tab' => 'sell']) }}" class="{{ $tab === 'sell' ? 'active' : '' }}">出品した商品</a>
        {{-- 購入タブのリンク --}}
        <a href="{{ route('users.show', ['tab' => 'buy']) }}" class="{{ $tab === 'buy' ? 'active' : '' }}">購入した商品</a>
    </div>

    <div class="item-list-container">
        {{-- 出品商品一覧 --}}
        @if ($tab === 'sell')
            @if ($items->isEmpty())
                <p>出品した商品はありません</p>
            @else
                @foreach($items as $item)
                    <div class="item-card-wrapper">
                        <a href="{{ route('items.show', ['item' => $item->id]) }}" class="item-card__link">
                            <div class="item-card__content">
                                <div class="item-card__image-wrapper">
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="item-card__image" />
                                </div>
                                <div class="item-card__name-wrapper">
                                    <p class="item-card__name">{{ $item->name }}</p>
                                </div>
                            </div>
                        </a>
                        @if ($item->status === 'sold')
                            <div class="item-card__status">
                                <span class="status-text">Sold</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        @elseif ($tab === 'buy')
        {{-- 購入商品一覧 --}}
            @if ($purchases->isEmpty())
                <p>購入した商品はありません</p>
            @else
                @foreach($purchases as $purchase)
                    <div class="item-card-wrapper">
                        <a href="{{ route('items.show', ['item' => $purchase->item->id]) }}" class="item-card__link">
                            <div class="item-card__content">
                                <div class="item-card__image-wrapper">
                                    <img src="{{ asset('storage/' . $purchase->item->image) }}" alt="{{ $purchase->item->name }}" class="item-card__image" />
                                </div>
                                <div class="item-card__name-wrapper">
                                    <p class="item-card__name">{{ $purchase->item->name }}</p>
                                </div>
                            </div>
                        </a>
                        @if ($purchase->item->status === 'sold')
                            <div class="item-card__status">
                                <span class="status-text">Sold</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        @endif
    </div>
</div>

@endsection
