@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<div class="form__wrapper">
    <div class="form__heading">
        <h1>ログイン</h1>
    </div>
    <form class="form" action="/login" method="POST" novalidate>
        @csrf
        <div class="form__group">
            <div class="form__group-title">
                <label class="form__label--item">メールアドレス</label>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="email" name="email" value="{{ old('email') }}" />
                </div>
                <div class="form__error">
                    @error('email')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__group">
            <div class="form__group-title">
                <label class="form__label--item">パスワード</label>
            </div>
            <div class="form__group-content">
                <div class="form__input--text">
                    <input type="password" name="password" />
                </div>
                <div class="form__error">
                    @error('password')
                    {{ $message }}
                    @enderror
                </div>
            </div>
        </div>
        <div class="form__button">
            <button class="form__button-submit" type="submit">ログインする</button>
        </div>
    </form>
    <div class="auth__link">
        <a class="auth__link-anchor" href="/register">会員登録はこちら</a>
    </div>
</div>
@endsection