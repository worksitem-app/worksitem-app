@extends('errors.layout')
@section('title', 'Sayfanın süresi doldu')
@section('code', '419')
@section('heading', 'Sayfa uzun süre açık kaldı')
@section('message', 'Güvenlik için form bir süre sonra yenilenmeli. Geri dönüp formu tekrar gönderin; yazdıklarınız çoğu tarayıcıda korunur.')
@section('actions')
    <button class="btn primary" type="button" onclick="history.length > 1 ? history.back() : location.assign('/')">Geri dön</button>
    <a class="btn" href="/">Ana sayfa</a>
@endsection
