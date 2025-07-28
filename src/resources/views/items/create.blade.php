@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items/create.css') }}">
@endsection

@section('content')
<div class="form__container">
    <div class="form__heading">
        <h1 class="form__heading-title">商品の出品</h1>
    </div>
    <form class="form" action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{-- 商品画像 --}}
        <div class="form__group">
            <h3 class="form__group-title">
                <label class="form__label--item">商品画像</label>
            </h3>

            <div class="form__group-image">
                <label for="image" class="form__input--image">画像を選択する</label>
                <input type="file" id="image" name="image" style="display: none;" />
            </div>

            <div class="form__error">
                @error('image')
                    {{ $message }}
                @enderror
            </div>
        </div>
        <div class="form__detail-section">
            <h2 class="form__title">商品の詳細</h2>
            {{-- カテゴリー --}}
            <div class="form__group">
                <h3 class="form__group-title">
                    <label class="form__label--item">カテゴリー</label>
                </h3>
                <div class="form__group-content">
                    <div class="form__category-buttons">
                        @foreach ($categories as $category)
                            <label class="category-button">
                                <input type="checkbox" name="categories[]" value="{{ $category->id }}" hidden />
                                {{ $category->content }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="form__error">
                    @error('categories')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <h3 class="form__group-title">
                    <label class="form__label--item">商品の状態</label>
                </h3>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <select class="form__select" name="condition">
                            <option value="" disabled selected>選択してください</option>
                            <option value="良好">良好</option>
                            <option value="目立った傷や汚れなし">目立った傷や汚れなし</option>
                            <option value="やや傷や汚れあり">やや傷や汚れあり</option>
                            <option value="状態が悪い">状態が悪い</option>
                        </select>
                        <div class="form__error">
                            @error('condition')
                                {{ $message }}
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form__detail-section">
            <h2 class="form__title">商品名と説明</h2>
            <div class="form__group">
                <h3 class="form__group-title">
                    <label class="form__label--item">商品名</label>
                </h3>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="name" value="{{ old('name') }}" />
                    </div>
                </div>
                <div class="form__error">
                    @error('name')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <h3 class="form__group-title">
                    <label class="form__label--item">ブランド名</label>
                </h3>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="text" name="brand" value="{{ old('brand') }}" />
                    </div>
                </div>
                <div class="form__error">
                    @error('brand')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__group">
            <h3 class="form__group-title">
                <label class="form__label--item">商品の説明</label>
            </h3>
            <div class="form__group-content">
                <div class="form__input--text">
                    <textarea name="description"> {{ old('description') }}</textarea>
                </div>
                <div class="form__error">
                    @error('description')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__group">
            <h3 class="form__group-title">
                <label class="form__label--item">販売価格</label>
            </h3>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="text" name="price" value="{{ old('price') }}" placeholder="¥"/>
                </div>
                <div class="form__error">
                    @error('price')
                        {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__button">
            <button class="form__button-submit" type="submit">出品する</button>
        </div>
    </form>
</div>
@endsection
