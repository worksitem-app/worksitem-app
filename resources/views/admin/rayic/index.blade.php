@extends('layouts.app')

@section('content')

<div style="max-width:1200px;margin:30px auto;padding:20px;">

    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h1>Rayiç Fiyat Yönetimi</h1>

        <a href="{{ route('admin.rayic.create') }}">
            + PDF Yükle
        </a>
    </div>

    @if(session('success'))
        <div style="padding:15px;background:#dcfce7;color:#166534;margin:20px 0;">
            {{ session('success') }}
        </div>
    @endif

    <table style="width:100%;border-collapse:collapse;margin-top:25px;">
        <thead>
            <tr>
                <th>Dönem</th>
                <th>Kaynak</th>
                <th>PDF</th>
                <th>Kayıt</th>
                <th>Değişen</th>
                <th>Durum</th>
                <th></th>
            </tr>
        </thead>

        <tbody>
            @foreach($imports as $import)
                <tr>
                    <td>{{ $import->period }}</td>
                    <td>{{ $import->source }}</td>
                    <td>{{ $import->file_name }}</td>
                    <td>{{ $import->valid_records }}</td>
                    <td>{{ $import->changed_records }}</td>
                    <td>{{ $import->status }}</td>
                    <td>
                        <a href="{{ route('admin.rayic.preview', $import->id) }}">
                            Görüntüle
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:20px;">
        {{ $imports->links() }}
    </div>

</div>

@endsection
