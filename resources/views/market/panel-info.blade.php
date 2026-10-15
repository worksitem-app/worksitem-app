@php
    $title = 'Hizmet bilgileri';
@endphp
@include('market._layout-top')
<main class="mk-wrap" style="max-width:760px">
    @if ($onboarding)
        @if (session('firm_new'))<div class="mk-ok" role="status">Başvurunuz alındı. Başvuru no: <b>{{ session('firm_new') }}</b>. Worksitem ekibi vergi bilgilerinizi en geç 2 iş günü içinde kontrol edip SMS ile dönecek.</div>@endif
    @else
        @include('market._panel-head')
    @endif

    <div class="mk-card">
        <small style="color:var(--muted)">{{ $onboarding ? 'Son adım · isteğe bağlı · yaklaşık 1 dakika' : 'Firma paneli' }}</small>
        <h1 style="margin:4px 0 8px;font-size:24px">{{ $firm->name }}: {{ $supplier ? 'sattığınız ürünler' : 'yaptığınız işler' }}</h1>
        <p style="color:var(--muted);line-height:1.6;margin:0 0 6px">Bu sorular size <b>daha uygun iş ve daha isabetli teklif daveti</b> göndermemiz için. Yanıtlamak zorunlu değil; istediğiniz zaman firma panelinden değiştirebilirsiniz.</p>
        <p style="color:var(--muted);line-height:1.6;margin:0 0 16px;font-size:13.5px">Yazdığınız fiyatlar müşterilere ve diğer firmalara <b>gösterilmez</b>. Yalnızca size uygun işleri seçmek ve Worksitem hesaplayıcılarındaki bölge fiyatlarını doğrulamak için kullanılır. Fiyatlar KDV dahil, yaklaşık olarak yazılabilir.</p>
        @if ($errors->any())<div class="mk-err">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('market.panel.info.save') }}" class="fm-form" style="grid-template-columns:1fr;border:0;padding:0" id="infoForm">
            @csrf
            @foreach ($questions as $k => $q)
                @php($v = old("a.$k", $answers[$k] ?? null))
                <div class="mk-q" @if (! empty($q['when'])) data-when="{{ $q['when'] }}" @endif>
                    <label class="f" for="q-{{ $k }}">{{ $q['q'] }}</label>
                    @if ($q['type'] === 'money')
                        <div class="row" style="grid-template-columns:1fr auto;align-items:center"><input type="number" id="q-{{ $k }}" name="a[{{ $k }}]" value="{{ $v }}" min="1" step="1" inputmode="numeric" placeholder="ör. {{ $k === 'min_job' ? '20000' : '' }}"><span style="color:var(--muted);font-size:13px;white-space:nowrap">{{ $q['unit'] }}</span></div>
                    @elseif ($q['type'] === 'select')
                        <div class="fm-pills">@foreach ($q['options'] as $ok => $ol)<label><input type="radio" name="a[{{ $k }}]" value="{{ $ok }}" @checked((string) $v === (string) $ok)> {{ $ol }}</label>@endforeach</div>
                    @elseif ($q['type'] === 'multi')
                        <div class="fm-pills" data-groups>@foreach ($q['options'] as $ok => $ol)<label><input type="checkbox" name="a[{{ $k }}][]" value="{{ $ok }}" @checked(in_array($ok, (array) $v, true))> {{ $ol }}</label>@endforeach</div>
                    @else
                        <input type="text" id="q-{{ $k }}" name="a[{{ $k }}]" value="{{ $v }}" maxlength="300">
                    @endif
                    @if (! empty($q['hint']))<small style="color:var(--muted);display:block;margin-top:4px">{{ $q['hint'] }}</small>@endif
                </div>
            @endforeach
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:6px">
                <button class="fm-btn" type="submit">Kaydet</button>
                <button class="fm-btn o" type="submit" name="skip" value="1" formnovalidate>{{ $onboarding ? 'Şimdi değil' : 'Vazgeç' }}</button>
            </div>
        </form>
    </div>
</main>
<style>.mk-q{padding:12px 0;border-bottom:1px solid var(--line)}.mk-q:last-of-type{border-bottom:0}.mk-q[hidden]{display:none}</style>
<script>
(function () {
    var groups = document.querySelector('[data-groups]');
    if (!groups) return;
    function sync() {
        var on = Array.prototype.map.call(groups.querySelectorAll('input:checked'), function (i) { return i.value; });
        document.querySelectorAll('[data-when]').forEach(function (q) { q.hidden = on.indexOf(q.getAttribute('data-when')) < 0; });
    }
    groups.addEventListener('change', sync); sync();
})();
</script>
<x-site.contech-footer />
</body>
</html>
