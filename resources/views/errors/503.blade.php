@extends('errors.layout')
@if (! empty($busy))
    {{-- Veritabanı bağlantı sınırı / yoğunluk (bootstrap/app.php) --}}
    @section('title', 'Yoğunluk var')
    @section('code', '503')
    @section('heading', 'Şu an çok yoğunuz')
    @section('message', 'Sunucu kısa süreli yoğunluk yaşıyor. 30 saniye kadar sonra sayfayı yenileyin. Hesaplamalarınız kaybolmaz.')
@else
    @section('title', 'Kısa bakım')
    @section('code', 'Bakım')
    @section('heading', 'Kısa bir bakım yapıyoruz')
    @section('message', 'Site birkaç dakika içinde geri gelecek. Hesaplamalarınız kaybolmaz.')
@endif
@section('actions')
    <button class="btn primary" type="button" onclick="location.reload()">Yenile</button>
@endsection
