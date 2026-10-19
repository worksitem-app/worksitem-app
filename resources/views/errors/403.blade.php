@extends('errors.layout')
@section('title', 'Erişim yok')
@section('code', '403')
@section('heading', 'Bu sayfayı açma yetkiniz yok')
@section('message', 'Bağlantı süresi dolmuş ya da bu sayfa yalnız yetkili hesaplara açık olabilir.')
@section('actions')
    <a class="btn primary" href="/">Ana sayfa</a>
    <a class="btn" href="/araclar">Tüm araçlar</a>
@endsection
