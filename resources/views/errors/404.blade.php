@extends('errors.layout')
@section('title', 'Sayfa bulunamadı')
@section('code', '404')
@section('heading', 'Bu sayfa yok ya da taşındı')
@section('message', 'Bağlantı eski olabilir. Aradığınız hesaplama büyük ihtimalle araçlar sayfasında.')
@section('actions')
    <a class="btn primary" href="/araclar">Tüm araçlar</a>
    <a class="btn" href="/">Ana sayfa</a>
@endsection
@section('links')
    <li><a href="/maliyet-hesapla">Tadilat maliyeti hesapla</a></li>
    <li><a href="/araclar/banyo-maliyet-hesaplayici">Banyo maliyeti</a></li>
    <li><a href="/araclar/boya-hesaplayici">Boya hesaplayıcı</a></li>
    <li><a href="/araclar/mutfak-planlayici">Mutfak planlayıcı</a></li>
@endsection
