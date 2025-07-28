@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/users/edit.css') }}">
@endsection

@section('content')
<div class="form__container">
    <div class="form__heading">
        <h2>プロフィール設定</h2>
    </div>
    <form class="form" action="{{ route('users.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        {{-- プロフィール画像 --}}
        <div class="form__group">
            <div class="form__image-wrapper">
                @if ($user->profile_image)
                    <img src="{{ asset('storage/' . $user->profile_image) }}" alt="プロフィール画像" class="form__image" />
                @else
                    {{-- 画像が未登録の場合、グレーの円を表示 --}}
                    <div class="form__placeholder-image"></div>
                @endif
                    <div class="form__input">
                        <label for="profile_image" class="form__input--file">画像を選択する</label>
                        <input type="file" id="profile_image" name="profile_image" style="display: none;" />
                    </div>
            </div>
            <div class="form__error">
                @error('profile_image')
                    {{ $message }}
                @enderror
            </div>
        </div>
        {{-- ユーザー名 --}}
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">ユーザー名</span>
            </div>
            <div class="form__group-input">
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form__input--text"/>
            </div>
            <div class="form__error">
                @error('name')
                    {{ $message }}
                @enderror
            </div>
        </div>
        {{-- 郵便番号 --}}
        <div class="form__group">
            <div class="form__group-title">
                <span class="form__label--item">郵便番号</span>
            </div>
            <div class="form__group-input">
                <input type="text" name="postal_code" value="{{ old('postal_code', $address->postal_code ?? '') }}" class="form__input--text"/>
                {{-- $postal_codeが存在すればその値を、なければ空文字を表示 --}}
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
                <span class="form__label--item">住所</span>
            </div>
            <div class="form__group-input">
                <input type="text" name="address" value="{{ old('address', $address->address ?? '') }}"class="form__input--text"/>
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
                <span class="form__label--item">建物名</span>
            </div>
            <div class="form__group-input">
                <input type="text" name="building" value="{{ old('building', $address->building ?? '') }}" class="form__input--text"/>
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
