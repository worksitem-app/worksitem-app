@extends('errors.layout')
@section('title', 'Kısa bakım')
@section('code', 'Bakım')
@section('heading', 'Kısa bir bakım yapıyoruz')
@section('message', 'Site birkaç dakika içinde geri gelecek. Hesaplamalarınız kaybolmaz.')
@section('actions')
    <button class="btn primary" type="button" onclick="location.reload()">Yenile</button>
@endsection
