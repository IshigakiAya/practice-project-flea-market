@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/show.css') }}">
@endsection

@section('content')
<div class="item-info-container">
    <div class="item-info__image-wrapper">
        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="item-info__image" />
    </div>
    <div class="item-info__content-wrapper">
        <div class="item-info__data-group">
            <h1 class="item-info__data-name">{{ $item->name }}</h1>
            <p class="item-info__data-brand">
                {{ $item->brand ?? 'ブランド名' }}
            </p>
            <p class="item-info__data-price">
                ¥<span style="font-size: 30px;">{{ number_format($item->price) }}</span>(税込)
            </p>
            <div class="item-info__data-meta">
                {{-- いいねボタン --}}
                <div class="likes-count">
                    <form method="POST" action="{{ route('likes.store', $item->id) }}">
                        @csrf
                        @if (auth()->check() && auth()->user()->likes->contains($item->id))
                            <button class="likes-icon" type="submit"><i class="fa-solid fa-star"></i></button> {{--いいね付与--}}
                        @else
                            <button class="likes-icon" type="submit"><i class="fa-regular fa-star"></i></button> {{--いいね削除--}}
                        @endif
                    </form>
                    <p class="count-number">{{ $likesCount }}</p>
                </div>
                <div class="comments-count">
                    <div class="comments-icon">
                        <i class="fa-regular fa-comment"></i>
                    </div>
                    <p class="count-number">{{ $commentsCount }}</p>
                </div>
            </div>
        </div>

        <a class="item-info__purchase-button" href="{{ route('purchases.create', ['item' => $item->id]) }}">購入手続きへ</a>

        <div class="item-info__description-group">
            <h2>商品説明</h2>
            <div>{{ $item->description }}</div>
        </div>
        <div class="item-info__details-group">
            <h2>商品の情報</h2>
            <div class="item-details">
                <div class="detail-row">
                    <div class="detail-label">カテゴリー</div>
                    <div class="category-tags-wrapper">
                        @foreach($item->categories as $category)
                            <span class="item-category-tag">
                                {{ $category->name }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="detail-row">
                    <div class="detail-label">商品の状態</div>
                    <div class="item-condition">
                        {{ $item->condition }}
                    </div>
                </div>
            </div>
        </div>
        <div class="item-info__comment-group">
            <h2 class="comment-group-title">コメント({{ $commentsCount }})</h2>
            @if($item->comments->isEmpty())
                <p>まだコメントはありません</p>
            @else
                @foreach($item->comments as $comment)
                    <div class="author-profile">
                        @if ($comment->user && $comment->user->profile_image)
                            <img src="{{ asset('storage/' . $comment->user->profile_image) }}" alt="プロフィール画像" class="author-profile__image" />
                        @else
                            {{-- 画像が未登録の場合、グレーの円を表示 --}}
                            <div class="author-profile__placeholder-image"></div>
                        @endif
                        <p class="author-profile__name">{{ $comment->user->name }}</p>
                    </div>
                    <div class="comment-wrapper">
                        <p class="comment__content">{{ $comment->comment }}</p>
                    </div>
                @endforeach
            @endif
            <div class="comment-group__form">
                <label class="form-title">商品へのコメント</label>
                <form class="form" action="{{ route('comments.store', ['item' => $item->id]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="item_id" value="{{ $item->id }}" />
                    <textarea name="comment" class="form__input"></textarea>
                    <div class="form__error">
                        @error('comment')
                            {{ $message }}
                        @enderror
                    </div>

                    <div class="form__button">
                        @auth
                            <button type="submit" class="form__button-submit">コメントを送信する</button>
                        @else
                            <a href="/login" class="btn-login-redirect">コメントを送信する</a>
                        @endauth
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
