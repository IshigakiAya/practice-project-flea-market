@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}">
@endsection

@section('content')
<div class="verify-email-container">
    <div class="message-wrapper">
        <p class="message">登録していただいたメールアドレスに認証メールを送付しました。</p>
        <p class="message">メール認証を完了してください。</p>
    </div>
    <div class="link-wrapper">
        <a href="http://localhost:8025" class="link" target="_blank" rel="noopener noreferrer">
        認証はこちらから
        </a>
    </div>
    <form class="form" method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button class="button" type="submit">認証メールを再送する</button>
    </form>
</div>
@endsection