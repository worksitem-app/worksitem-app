@extends('layouts.app')

@section('content')

<div style="max-width:900px;margin:40px auto;padding:24px;">

    <h1>Rayiç Fiyat PDF Yükleme</h1>

    <p style="color:#666;">
        PDF yükleyin. Gemini PDF içindeki rayiç tablolarını okuyacak
        ve verileri veritabanına aktarılmadan önce önizleyecektir.
    </p>

    @if($errors->any())
        <div style="padding:15px;background:#fee2e2;color:#991b1b;margin:20px 0;">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('admin.rayic.analyze') }}"
        enctype="multipart/form-data"
    >
        @csrf

        <div style="margin-bottom:20px;">
            <label>Kaynak</label>
            <select
                name="source"
                style="width:100%;padding:12px;"
            >
                <option value="YFK">YFK</option>
            </select>
        </div>

        <div style="margin-bottom:20px;">
            <label>Dönem</label>
            <input
                type="month"
                name="period"
                value="{{ old('period', now()->format('Y-m')) }}"
                required
                style="width:100%;padding:12px;"
            >
        </div>

        <div style="margin-bottom:20px;">
            <label>Rayiç PDF</label>

            <input
                type="file"
                name="pdf"
                accept="application/pdf,.pdf"
                required
                style="width:100%;padding:20px;border:2px dashed #ccc;"
            >
        </div>

        <button
            type="submit"
            style="padding:14px 24px;cursor:pointer;"
        >
            PDF'Yİ ANALİZ ET
        </button>
    </form>

</div>

@endsection
