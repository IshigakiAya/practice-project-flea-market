@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/index.css') }}">
@endsection

@section('content')
<div class="tabs">
    {{-- おすすめタブのリンク --}}
    <a href="{{ route('items.index', ['keyword' => request('keyword')]) }}" class="{{ request('tab') != 'mylist' ? 'active' : '' }}">おすすめ</a>

    {{-- マイリストタブのリンク --}}
    <a href="{{ route('items.index', ['tab' => 'mylist', 'keyword' => request('keyword')]) }}" class="{{ request('tab') == 'mylist' ? 'active' : '' }}">マイリスト</a>
</div>

{{-- 商品情報の表示 --}}
<div class="item-list-container">
    @foreach($items as $item)
    <div class="item-card-wrapper">
        <a href="{{ route('items.show', ['item' =>$item->id]) }}" class="item-card__link">
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
</div>
@endsection
