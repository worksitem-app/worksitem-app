@props(['short' => false])
@php
    $fee = number_format((int) config('worksitem_bathroom.inspection_fee', 2500), 0, ',', '.');
    $vat = config('worksitem_bathroom.inspection_vat_included', false) ? 'KDV dahil' : '+ KDV';
    $days = (int) config('worksitem_bathroom.inspection_credit_days', 15);
@endphp
@if ($short)Yerinde keşif bedeli {{ $fee }} TL ({{ $vat }}); {{ $days }} gün içinde Kainta İnşaat ile sözleşme imzalarsanız iş bedelinden düşülür.@else
Yerinde keşif bedeli {{ $fee }} TL'dir ({{ $vat }}) ve keşif randevusu onaylanmadan önce yazılı olarak bildirilir. Keşif tarihinden itibaren {{ $days }} gün içinde aynı iş için Kainta İnşaat ile yazılı sözleşme imzalanırsa, ödediğiniz keşif bedeli toplam iş bedelinden düşülür. Sözleşme imzalanmazsa keşif bedeli iade edilmez; keşif raporu size kalır.@endif
