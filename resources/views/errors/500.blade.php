@extends('errors.layout')
@section('title', 'Bir şeyler ters gitti')
@section('code', '500')
@section('heading', 'Bizden kaynaklı bir sorun oldu')
@section('message', 'Hata kaydedildi, bakıyoruz. Birkaç dakika sonra tekrar deneyin.')
@section('actions')
    <button class="btn primary" type="button" onclick="location.reload()">Tekrar dene</button>
    <a class="btn" href="/araclar">Tüm araçlar</a>
@endsection
