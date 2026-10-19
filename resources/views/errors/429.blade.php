@extends('errors.layout')
@section('title', 'Çok fazla istek')
@section('code', '429')
@section('heading', 'Biraz yavaşlayalım')
@section('message', 'Kısa sürede çok fazla istek geldi. Bir dakika bekleyip tekrar deneyin.')
@section('actions')
    <button class="btn primary" type="button" onclick="location.reload()">Tekrar dene</button>
    <a class="btn" href="/">Ana sayfa</a>
@endsection
