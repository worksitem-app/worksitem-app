@extends('layouts.app')

@section('content')

<div style="max-width:1400px;margin:30px auto;padding:20px;">

    <h1>Rayiç Analiz Önizleme</h1>

    <div style="padding:20px;background:#f5f5f5;margin:20px 0;">
        <strong>{{ $import->file_name }}</strong><br>
        Kaynak: {{ $import->source }}<br>
        Dönem: {{ $import->period }}<br>
        Toplam: {{ $import->total_records }}<br>
        Geçerli: {{ $import->valid_records }}<br>
        Hatalı: {{ $import->invalid_records }}<br>
        Değişen: {{ $import->changed_records }}
    </div>

    @if($import->changed_records > 0)
        <div style="padding:15px;background:#fff7ed;color:#9a3412;margin-bottom:20px;">
            Bu PDF mevcut kayıtlarla karşılaştırıldığında
            <strong>{{ $import->changed_records }}</strong>
            fiyat değişikliği tespit edildi.
        </div>
    @endif

    <div style="overflow:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr>
                    <th>Poz No</th>
                    <th>Açıklama</th>
                    <th>Birim</th>
                    <th>Eski</th>
                    <th>Yeni</th>
                    <th>Sayfa</th>
                    <th>Durum</th>
                </tr>
            </thead>

            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->poz_no }}</td>

                        <td>
                            {{ $item->description }}
                        </td>

                        <td>
                            {{ $item->unit }}
                        </td>

                        <td>
                            @if($item->old_price !== null)
                                {{ number_format($item->old_price, 2, ',', '.') }}
                            @else
                                —
                            @endif
                        </td>

                        <td>
                            {{ number_format($item->price, 2, ',', '.') }}
                        </td>

                        <td>
                            {{ $item->source_page ?? '—' }}
                        </td>

                        <td>
                            @if($item->validation_status === 'valid')
                                @if($item->old_price !== null && $item->old_price != $item->price)
                                    <strong style="color:#b45309;">
                                        DEĞİŞTİ
                                    </strong>
                                @else
                                    <span style="color:green;">
                                        OK
                                    </span>
                                @endif
                            @else
                                <strong style="color:red;">
                                    HATALI
                                </strong>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:25px;display:flex;gap:10px;">

        <form
            method="POST"
            action="{{ route('admin.rayic.approve', $import->id) }}"
        >
            @csrf

            <button
                type="submit"
                onclick="return confirm('Bu rayiçleri veritabanına aktarmak istediğinize emin misiniz?')"
                style="padding:14px 24px;cursor:pointer;"
            >
                ✓ TÜM GEÇERLİ KAYITLARI ONAYLA
            </button>
        </form>

        <a
            href="{{ route('admin.rayic.index') }}"
            style="padding:14px 24px;"
        >
            Vazgeç
        </a>

    </div>

    <div style="margin-top:25px;">
        {{ $items->links() }}
    </div>

</div>

@endsection
