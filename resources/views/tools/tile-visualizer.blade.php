<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Seramik Görselleştirici | Fayansı Mekânında AI ile Gör | WORKSİTEM</title>
<meta name="description" content="Fotoğrafınızı yükleyin, seramik veya fayans desenini seçin. Worki ile zemininizi, duvarınızı veya duş alanınızı AI ile mekânınızda görün.">
<link rel="canonical" href="https://worksitem.com/araclar/seramik-gorsellestirici">
<style>
:root{--bg:#f4f6f8;--card:#fff;--text:#101318;--muted:#667085;--line:#e5e7eb;--accent:#ffbd22;--accent2:#ffe8a7;--dark:#111827;--ok:#067647;--danger:#b42318;--shadow:0 12px 35px rgba(16,24,40,.07)}
*{box-sizing:border-box}body{margin:0;background:var(--bg);color:var(--text);font-family:Inter,system-ui,-apple-system,Segoe UI,sans-serif}.wrap{max-width:1400px;margin:0 auto;padding:34px 20px 64px}.hero{display:flex;justify-content:space-between;align-items:flex-start;gap:20px;margin-bottom:20px}.hero h1{font-size:38px;line-height:1.05;margin:0 0 10px;letter-spacing:-1.5px}.hero p{margin:0;color:var(--muted);max-width:820px}.badge{display:inline-flex;align-items:center;gap:6px;padding:7px 11px;border-radius:999px;background:var(--accent2);font-size:12px;font-weight:900;margin-left:10px;vertical-align:middle}.credit{background:var(--dark);color:#fff;border-radius:14px;padding:12px 15px;font-weight:800;white-space:nowrap}.steps{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;background:#fff;border:1px solid var(--line);border-radius:18px;padding:12px;margin-bottom:18px;box-shadow:var(--shadow)}.step{display:flex;gap:12px;align-items:center;padding:14px}.step-no{width:40px;height:40px;border-radius:50%;display:grid;place-items:center;background:var(--accent);font-weight:900}.step strong{display:block;font-size:15px}.step span{display:block;color:var(--muted);font-size:12px;margin-top:2px}.app{display:grid;grid-template-columns:420px minmax(0,1fr);gap:18px}.panel,.preview-card{background:#fff;border:1px solid var(--line);border-radius:18px;box-shadow:var(--shadow)}.panel{padding:20px}.section{margin-bottom:20px}.section h3{font-size:14px;margin:0 0 10px}.upload{border:1.5px dashed #cfd4dc;border-radius:14px;padding:18px;text-align:center;background:#fafafa;cursor:pointer;position:relative;overflow:hidden}.upload input{position:absolute;inset:0;opacity:0;cursor:pointer}.upload strong{display:block}.upload small{color:var(--muted)}.room-thumb{width:100%;aspect-ratio:16/9;border-radius:12px;object-fit:cover;display:none;margin-top:10px;border:1px solid var(--line)}.seg,.source,.sizes,.styles{display:flex;flex-wrap:wrap;gap:8px}.chip{border:1px solid var(--line);background:#f8fafc;color:var(--text);padding:9px 12px;border-radius:999px;font-weight:800;font-size:12px;cursor:pointer}.chip.active{background:var(--dark);color:#fff;border-color:var(--accent);box-shadow:0 0 0 2px var(--accent)}.source .chip.active{background:var(--accent);color:#111;border-color:var(--accent);box-shadow:none}.tiles{display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-top:10px}.tile{aspect-ratio:1;border-radius:10px;border:2px solid transparent;cursor:pointer;background-size:cover;background-position:center;position:relative}.tile.active{border-color:var(--accent);box-shadow:0 0 0 2px #111}.tile-title{font-size:10px;margin-top:5px;color:var(--muted);text-align:center}.measure{display:grid;grid-template-columns:1fr 1fr;gap:10px}.measure label{font-size:11px;font-weight:800;color:var(--muted)}.measure input{width:100%;height:44px;border:1px solid var(--line);border-radius:10px;padding:0 11px;margin-top:5px}.note{font-size:11px;color:#475467;background:#eef4ff;border-radius:10px;padding:9px 10px}.preview-card{padding:14px}.preview-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}.preview-head h2{font-size:17px;margin:0}.tabs{display:flex;gap:6px}.tab{border:1px solid var(--line);background:#f8fafc;border-radius:9px;padding:8px 11px;font-size:12px;font-weight:800;cursor:pointer}.tab.active{background:var(--accent);border-color:var(--accent)}.preview-grid{display:grid;grid-template-columns:minmax(0,1fr) 300px;gap:12px}.photo-stage{min-height:500px;border-radius:14px;background:#111;position:relative;overflow:hidden;display:grid;place-items:center}.photo-stage img{width:100%;height:100%;object-fit:cover;position:absolute;inset:0}.empty{color:#fff;text-align:center;z-index:2;padding:30px}.empty strong{font-size:22px;display:block;margin-bottom:7px}.watermark{position:absolute;inset:0;display:none;align-items:center;justify-content:center;z-index:4;pointer-events:none;background:linear-gradient(135deg,rgba(17,24,39,.03),rgba(17,24,39,.15))}.watermark span{transform:rotate(-22deg);font-size:52px;font-weight:1000;color:rgba(255,255,255,.35);text-shadow:0 2px 10px rgba(0,0,0,.25)}.ai-loading{position:absolute;inset:0;display:none;place-items:center;background:rgba(17,24,39,.72);color:#fff;z-index:6;text-align:center;padding:30px}.ai-loading.active{display:grid}.plan{border:1px solid var(--line);border-radius:14px;padding:12px;background:#fafafa}.plan h3{font-size:13px;margin:0 0 10px}.plan canvas{width:100%;aspect-ratio:4/5;background:#fff;border:1px solid #d0d5dd;border-radius:7px}.plan-meta{font-size:11px;color:var(--muted);margin-top:10px}.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:var(--line);border-radius:14px;overflow:hidden;margin-top:12px}.stat{background:#fff;padding:14px}.stat span{font-size:10px;color:var(--muted);display:block}.stat strong{font-size:14px;display:block;margin-top:3px}.actions{display:grid;grid-template-columns:1fr auto;gap:10px;margin-top:12px}.btn{height:54px;border:0;border-radius:12px;font-size:15px;font-weight:900;cursor:pointer}.btn-main{background:var(--accent);color:#111}.btn-secondary{background:#111;color:#fff;padding:0 20px;text-decoration:none;display:grid;place-items:center}.locked{background:#fff8e7;border:1px solid #ffe0a0;border-radius:12px;padding:12px;margin-top:10px;font-size:12px}.custom-box{display:none;margin-top:10px}.custom-box.active{display:block}.custom-box input{width:100%}.error{display:none;background:#fef3f2;color:var(--danger);border:1px solid #fecdca;padding:10px;border-radius:10px;font-size:12px;margin-top:10px}.error.active{display:block}.success{color:var(--ok)}
@media(max-width:1050px){.app{grid-template-columns:1fr}.preview-grid{grid-template-columns:1fr}.plan canvas{aspect-ratio:16/7}.photo-stage{min-height:420px}}@media(max-width:720px){.wrap{padding:22px 12px 48px}.hero{display:block}.credit{display:inline-block;margin-top:12px}.hero h1{font-size:30px}.steps{grid-template-columns:1fr}.tiles{grid-template-columns:repeat(3,1fr)}.stats{grid-template-columns:1fr 1fr}.actions{grid-template-columns:1fr}.photo-stage{min-height:330px}.panel{padding:16px}}

/* TILE_VISUALIZER_FINAL_MOBILE_V5 */
.upload{display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:104px}
.size-select{width:100%;height:48px;border:1px solid var(--line);border-radius:12px;background:#fff;color:var(--text);padding:0 12px;font:inherit;font-weight:750;outline:none}
.size-select:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(255,184,0,.16)}
.custom-tile-size{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:10px}
.custom-tile-size[hidden]{display:none!important}
.custom-tile-size label{font-size:11px;font-weight:800;color:var(--muted)}
.custom-tile-size input{width:100%;height:44px;border:1px solid var(--line);border-radius:10px;padding:0 10px;margin-top:5px}
.size-help{display:block;color:var(--muted);font-size:10px;line-height:1.4;margin-top:7px}
#catalogTiles{grid-template-columns:repeat(3,minmax(0,1fr))!important;gap:8px!important;margin-top:10px}
#catalogTiles .tile-item{min-width:0;min-height:124px!important;display:flex;flex-direction:column;align-items:center;justify-content:flex-start;padding:7px!important;background:#fff;border:1px solid var(--line);border-radius:12px;overflow:hidden}
#catalogTiles .tile{min-height:0!important;aspect-ratio:auto!important;background-size:cover!important;background-position:center!important;background-repeat:no-repeat!important;flex:0 0 auto;border-radius:8px}
#catalogTiles .tile-title{width:100%;margin-top:auto!important;padding:7px 2px 1px!important;text-align:center;color:var(--text)!important;font-size:10px!important;line-height:1.15;font-weight:850!important;white-space:normal}
#catalogTiles .tile.active{border-color:var(--accent)!important;box-shadow:0 0 0 2px #111}
@media(max-width:1050px){.app{grid-template-columns:1fr!important}.preview-grid{grid-template-columns:1fr!important}.plan canvas{aspect-ratio:16/7!important}}
@media(max-width:720px){
 .wrap{padding:14px 10px 40px!important}.hero{display:block}.hero h1{font-size:27px!important}.hero p{font-size:14px}.credit{display:inline-block;margin-top:10px}.steps{display:none!important}
 .panel,.preview-card{width:100%!important;margin-bottom:12px}.panel{padding:14px!important}.section{margin-bottom:17px}.seg,.source,.styles{gap:6px}.chip{padding:9px 10px;font-size:12px}
 #catalogTiles{grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:8px!important}
 #catalogTiles .tile-item{min-height:128px!important;padding:7px!important}
 #catalogTiles .tile-title{font-size:10.5px!important}
 .size-select{font-size:16px}.custom-tile-size{grid-template-columns:1fr 1fr}
 .preview-grid{grid-template-columns:1fr!important}.photo-stage{min-height:300px!important}.photo-stage img{object-fit:contain!important;background:#111}.plan{margin-top:8px}.plan canvas{aspect-ratio:4/3!important}
 .stats{grid-template-columns:1fr 1fr!important}.actions{grid-template-columns:1fr!important}.actions .btn,.actions a{width:100%}.preview-head{align-items:flex-start;gap:8px;flex-direction:column}.tabs{width:100%;display:grid;grid-template-columns:1fr 1fr}.tab{text-align:center}
}
@media(max-width:420px){.custom-tile-size{grid-template-columns:1fr}.stats{grid-template-columns:1fr!important}#catalogTiles .tile-item{min-height:122px!important}.watermark span{font-size:34px}}
</style>
<!-- VISUALIZER_SEO_V1 -->
<meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
<meta property="og:title" content="Seramik Görselleştirici | Seramiği Mekânında Gör – WORKSİTEM">
<meta property="og:description" content="Fotoğrafınızı yükleyin, seramik desenini ve karo ebadını seçin; Worki ile mekânınızda AI seramik önizlemesi oluşturun.">
<meta property="og:type" content="website">
<meta property="og:url" content="https://worksitem.com/araclar/seramik-gorsellestirici">
<script type="application/ld+json">
{
  "@@context":"https://schema.org",
  "@@type":"WebApplication",
  "name":"WORKSİTEM Seramik Görselleştirici",
  "url":"https://worksitem.com/araclar/seramik-gorsellestirici",
  "applicationCategory":"DesignApplication",
  "operatingSystem":"Web",
  "inLanguage":"tr-TR",
  "description":"Kullanıcının mekân fotoğrafı, seramik deseni ve karo ebadıyla AI destekli seramik önizlemesi oluşturmasına yardımcı olan web aracı.",
  "publisher":{"@@type":"Organization","name":"WORKSİTEM","url":"https://worksitem.com/"}
}
</script>
<script id="FAQ_SCHEMA_V1" type="application/ld+json">{"@@context":"https://schema.org","@@type":"FAQPage","mainEntity":[{"@@type":"Question","name":"Seramiği kendi odamda nasıl görebilirim?","acceptedAnswer":{"@@type":"Answer","text":"Oda fotoğrafınızı yükleyin, yüzeyi, karo desenini ve ebadını seçin; Worki ile AI önizlemesi oluşturun."}},{"@@type":"Question","name":"Kendi seramik görselimi yükleyebilir miyim?","acceptedAnswer":{"@@type":"Answer","text":"Evet. Kendi karom seçeneğiyle karo görselinizi yükleyebilirsiniz."}},{"@@type":"Question","name":"Karo ebadı görselleştirmeyi etkiler mi?","acceptedAnswer":{"@@type":"Answer","text":"Evet. Seçilen karo ebadı 2D yerleşim hesabında ve AI görselleştirme talimatında kullanılır."}},{"@@type":"Question","name":"AI sonucunu yeniden düzenleyebilir miyim?","acceptedAnswer":{"@@type":"Answer","text":"Evet. Kullanıcı yeni AI varyasyonları oluşturabilir."}}]}</script>
</head>
<body data-tool-slug="tile-visualizer">
<x-site.contech-header />
<main class="wrap">
    <div class="hero">
        <div>
            <h1>Seramiği Satın Almadan Önce Mekânınızda Görün <span class="badge">✨ AI Önizleme</span></h1>
            <p>Fotoğrafınızı yükleyin, katalogdan bir karo seçin veya kendi seramiğinizi ekleyin. Karo ebadını gerçek oranında görün ve Worki ile mekânınızda önizleyin.</p>
        </div>
        <div class="credit">Render Kredisi: <span id="creditCount">{{ $credits }}</span></div>
    </div>

    <section class="steps">
        <div class="step"><div class="step-no">1</div><div><strong>Fotoğraf Yükle</strong><span>Zemin, duvar veya banyo fotoğrafınızı ekleyin</span></div></div>
        <div class="step"><div class="step-no">2</div><div><strong>Karo Seç</strong><span>20 test deseni veya kendi seramik görseliniz</span></div></div>
        <div class="step"><div class="step-no">3</div><div><strong>AI Önizleme</strong><span>Karoyu kendi mekânınızda gerçekçi görün</span></div></div>
    </section>

    <div class="app">
        <aside class="panel">
            <div class="section">
                <h3>1. Mekân Fotoğrafı</h3>
                <label class="upload">
                    <input type="file" id="roomImage" accept="image/jpeg,image/png,image/webp">
                    <strong>📷 Fotoğraf yükle</strong>
                    <small>JPG, PNG, WEBP · en fazla 10 MB</small>
                </label>
                <img id="roomThumb" class="room-thumb" alt="Yüklenen mekân">
            </div>

            <div class="section">
                <h3>2. Yüzey Seçin</h3>
                <div class="seg" id="surfaceChips">
                    <button class="chip active" type="button" data-value="floor">Zemin</button>
                    <button class="chip" type="button" data-value="wall">Duvar</button>
                    <button class="chip" type="button" data-value="shower_wall">Duş Alanı</button>
                    <button class="chip" type="button" data-value="backsplash">Tezgah Arası</button>
                </div>
            </div>

            <div class="section">
                <h3>3. Karo Kaynağı</h3>
                <div class="source" id="sourceChips">
                    <button class="chip active" type="button" data-value="catalog">▦ Katalogdan Seç</button>
                    <button class="chip" type="button" data-value="custom">⇧ Kendi Seramiğini Yükle</button>
                </div>
                <div class="custom-box" id="customBox">
                    <input type="file" id="tileImage" accept="image/jpeg,image/png,image/webp">
                </div>
            </div>

            <div class="section">
                <h3>4. Karo Kataloğu</h3>
                <div class="styles" id="styleChips">
                    <button class="chip active" type="button" data-style="all">Tümü</button>
                    <button class="chip" type="button" data-style="Klasik">Klasik</button>
                    <button class="chip" type="button" data-style="Taş">Taş</button>
                    <button class="chip" type="button" data-style="Modern">Modern</button>
                    <button class="chip" type="button" data-style="Dekoratif">Dekor</button>
                </div>
                <div class="tiles" id="catalogTiles">
                    @foreach($catalog as $key => $tile)
                        <div class="tile-item" data-style="{{ $tile['style'] }}">
                            <button type="button" class="tile {{ $loop->first ? 'active' : '' }}" data-key="{{ $key }}" data-size="{{ $tile['size'] }}" data-name="{{ $tile['name'] }}" style="background-image:url('{{ $tile['preview_data'] }}')" aria-label="{{ $tile['name'] }} seç"></button>
                            <div class="tile-title">{{ $tile['name'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="section tile-size-section">
                <h3>5. Karo Ebadı</h3>
                <select id="tileSizeSelect" class="size-select" aria-label="Karo Ebadı">
                    <option value="30x60">30×60 cm</option>
                    <option value="20x20">20×20 cm</option>
                    <option value="60x120" selected>60×120 cm</option>
                    <option value="33x33">33×33 cm</option>
                    <option value="60x60">60×60 cm</option>
                    <option value="45x45">45×45 cm</option>
                    <option value="7.5x30">7,5×30 cm</option>
                    <option value="10x20">10×20 cm</option>
                    <option value="5x15">5×15 cm</option>
                    <option value="20x120">20×120 cm</option>
                    <option value="31.5x31.5">31,5×31,5 cm</option>
                    <option value="10x10">10×10 cm</option>
                    <option value="15x17">15×17 cm</option>
                    <option value="12x20">12×20 cm</option>
                    <option value="10x30">10×30 cm</option>
                    <option value="15x60">15×60 cm</option>
                    <option value="30x90">30×90 cm</option>
                    <option value="33x99">33×99 cm</option>
                    <option value="10x40">10×40 cm</option>
                    <option value="12x12">12×12 cm</option>
                    <option value="25x40">25×40 cm</option>
                    <option value="6.5x21.5">6,5×21,5 cm</option>
                    <option value="30x30">30×30 cm</option>
                    <option value="15x15">15×15 cm</option>
                    <option value="40x80">40×80 cm</option>
                    <option value="17x17">17×17 cm</option>
                    <option value="40x60">40×60 cm</option>
                    <option value="20x25">20×25 cm</option>
                    <option value="15x90">15×90 cm</option>
                    <option value="40x40">40×40 cm</option>
                    <option value="15x17.5">15×17,5 cm</option>
                    <option value="25x45">25×45 cm</option>
                    <option value="25x65">25×65 cm</option>
                    <option value="5x20">5×20 cm</option>
                    <option value="14x20">14×20 cm</option>
                    <option value="20x240">20×240 cm</option>
                    <option value="29.5x89">29,5×89 cm</option>
                    <option value="7x21">7×21 cm</option>
                    <option value="100x100">100×100 cm</option>
                    <option value="20x98">20×98 cm</option>
                    <option value="59x59">59×59 cm</option>
                    <option value="5x30">5×30 cm</option>
                    <option value="60x100">60×100 cm</option>
                    <option value="10x60">10×60 cm</option>
                    <option value="15x40">15×40 cm</option>
                    <option value="20x30">20×30 cm</option>
                    <option value="20x40">20×40 cm</option>
                    <option value="20x50">20×50 cm</option>
                    <option value="25x75">25×75 cm</option>
                    <option value="36x43">36×43 cm</option>
                    <option value="40x100">40×100 cm</option>
                    <option value="40x120">40×120 cm</option>
                    <option value="43x50">43×50 cm</option>
                    <option value="5x10">5×10 cm</option>
                    <option value="6x25">6×25 cm</option>
                    <option value="80x100">80×100 cm</option>
                    <option value="80x140">80×140 cm</option>
                    <option value="8x25">8×25 cm</option>
                    <option value="custom">Özel Ebat</option>
                </select>
                <div id="customTileSize" class="custom-tile-size" hidden>
                    <label>Genişlik (cm)<input type="number" id="customTileWidth" min="2" max="300" step="0.1" value="60"></label>
                    <label>Uzunluk (cm)<input type="number" id="customTileHeight" min="2" max="300" step="0.1" value="120"></label>
                </div>
                <small class="size-help">Seçtiğiniz ebat, katalog karosunun önizleme oranını ve 2D yerleşim hesabını birlikte değiştirir.</small>
            </div>

            <div class="section">
                <h3>6. Yüzey Ölçüsü</h3>
                <div class="measure">
                    <label>Genişlik (m)<input type="number" id="surfaceWidth" min="0.5" max="30" step="0.1" value="" placeholder="Örn. 5.00" required></label>
                    <label>Yükseklik / Uzunluk (m)<input type="number" id="surfaceHeight" min="0.5" max="30" step="0.1" value="" placeholder="Örn. 4.00" required></label><div id="dimensionSourceNote" style="margin-top:8px;font-size:12px;color:#667085">Doğru 2D yerleşim ve AI karo ölçeği için yüzey ölçülerini girin.</div>
                </div>
            </div>

            <div class="note">Mekân fotoğrafınız kalıcı saklanmaz. AI çıktısının HD sürümü, krediyle oluşturulan renderı indirebilmeniz için en fazla 24 saat geçici tutulur.</div>
        </aside>

        <section class="preview-card">
            <div class="preview-head">
                <h2>Önizleme</h2>
                <div class="tabs"><button class="tab active" type="button" data-view="photo">AI Görselleştirme</button><button class="tab" type="button" data-view="plan">2D Yerleşim</button></div>
            </div>

            <div class="preview-grid">
                <div class="photo-stage" id="photoStage">
                    <div class="empty" id="emptyState"><strong>Fotoğrafınızı yükleyin</strong><span>Seçtiğiniz karo burada önizlenecek.</span></div>
                    <img id="mainPreview" alt="Seramik önizleme">
                    <div class="watermark" id="watermark"><span>WORKSİTEM PREVIEW</span></div>
                    <div class="ai-loading" id="aiLoading"><div><strong>Gemini görselleştirmesi hazırlanıyor…</strong><br><small>Oda geometrisi korunarak karo uygulanıyor.</small></div></div>
                    <button type="button" class="preview-expand" id="previewExpandBtn" aria-label="Önizlemeyi tam ekran aç">⛶</button>
                    <button type="button" class="preview-close" id="previewCloseBtn" aria-label="Tam ekranı kapat">✕</button>
                </div>

                <aside class="plan">
                    <h3>2D Yerleşim Planı</h3>
                    <canvas id="layoutCanvas" width="720" height="520"></canvas>
                    <div class="plan-count" id="planCount">Karo yerleşim özeti</div>
                    <div class="plan-meta" id="planMeta">Ebat ve yüzey ölçüsüne göre yaklaşık yerleşim.</div>
                </aside>
            </div>

            <div class="stats">
                <div class="stat"><span>Seçilen Ebat</span><strong id="statSize">60 × 120 cm</strong></div>
                <div class="stat"><span>Kaplanan Alan</span><strong id="statArea">7,20 m²</strong></div>
                <div class="stat"><span>Tahmini Karo Adedi</span><strong id="statCount">10 adet</strong></div>
                <div class="stat"><span>Önizleme Türü</span><strong id="statType">2D + AI</strong></div>
            </div>

            <div class="actions">
                <button class="btn btn-main" id="renderBtn" type="button">✨ Worki ile Görselleştirmeyi Başlat</button>
                <a class="btn btn-secondary" href="/araclar/seramik-maliyet-hesaplayici" id="costLink">Metraj & Maliyet →</a>
                <button class="btn btn-download-locked" id="hdDownloadLocked" type="button">⬇ HD İndir · Önce render oluşturun</button>
            </div>

<div id="editPanel" style="display:none;margin-top:10px;padding:12px;border:1px solid #d0d5dd;border-radius:12px;background:#fff">
<strong>✨ Sonucu istediğiniz hale getirin</strong>
<div id="PROMPT_CHIPS_V1" style="display:flex;flex-wrap:wrap;gap:7px;margin:9px 0">
<button type="button" class="chip" data-edit-prompt="Derzleri incelt ve daha zarif göster.">Derzleri incelt</button>
<button type="button" class="chip" data-edit-prompt="Zemini daha açık ve ferah göster.">Daha açık</button>
<button type="button" class="chip" data-edit-prompt="Zemini daha koyu ve kontrastlı göster.">Daha koyu</button>
<button type="button" class="chip" data-edit-prompt="Daha modern ve sade bir görünüm oluştur.">Daha modern</button>
<button type="button" class="chip" data-edit-prompt="Karo görünümünü daha büyük ebatlı göster.">Karoyu büyüt</button>
</div>
<textarea id="editPrompt" maxlength="500" placeholder="Örn: Derzleri incelt, zemini daha açık yap, mekân geometrisini koru." style="width:100%;min-height:78px;margin-top:8px;padding:10px;border:1px solid #d0d5dd;border-radius:9px;font:inherit"></textarea>
<button type="button" id="editRenderBtn" class="btn btn-main" style="width:100%;margin-top:8px">✨ Yeni Varyasyon Oluştur · 1 Kredi</button>
</div>
            <div class="locked" id="paywall">💳 <strong><span id="testCreditCount">{{ $credits }}</span> kredi kaldı.</strong> · Her yeni AI varyasyonu 1 kredi.</div>
<div id="CREDIT_PAYWALL_V1" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(15,23,42,.72);align-items:center;justify-content:center;padding:18px">
<div style="width:min(620px,100%);background:#fff;border-radius:18px;padding:24px;box-shadow:0 24px 70px rgba(0,0,0,.28)">
<h2 style="margin:0 0 8px">✨ Tasarımınıza devam edin</h2>
<p style="margin:0 0 18px;color:#475467">AI varyasyonu oluşturmak için krediniz kalmadı. Bir paket seçerek kaldığınız yerden devam edin.</p>
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px">
<button type="button" data-credit-pack="5" class="btn">5 Kredi</button>
<button type="button" data-credit-pack="20" class="btn btn-main">⭐ 20 Kredi · Önerilen</button>
<button type="button" data-credit-pack="50" class="btn">50 Kredi</button>
</div>
<div id="creditPackMsg" style="margin-top:14px;font-size:13px;color:#667085">Her AI render veya yeni varyasyon 1 kredi kullanır.</div>
<button type="button" id="closeCreditPaywall" style="margin-top:16px;width:100%;border:0;background:none;cursor:pointer">Şimdilik kapat</button>
</div>
</div>
            <div class="error" id="errorBox"></div>
        </section>
    </div>
<section id="VISUALIZER_LLM_V1" class="panel" style="max-width:1400px;margin:18px auto;padding:22px">
<h2>Seramik Görselleştirici nedir?</h2>
<p>WORKSİTEM Seramik Görselleştirici; yüklediğiniz mekân fotoğrafına seçtiğiniz seramik, fayans veya karo desenini AI ile uygulamanıza yardımcı olur. Zemin, duvar, duş alanı ve tezgâh arası için önizleme oluşturabilirsiniz.</p>
<p><a href="/araclar/seramik-maliyet-hesaplayici"><strong>Seramik metraj ve maliyetini hesapla →</strong></a></p>
<h2>Sık Sorulan Sorular</h2>
<details><summary><strong>Seramiği kendi odamda nasıl görebilirim?</strong></summary><p>Oda fotoğrafınızı yükleyin, yüzeyi, karo desenini ve ebadını seçin; Worki ile AI önizlemesi oluşturun.</p></details>
<details><summary><strong>Kendi seramik görselimi yükleyebilir miyim?</strong></summary><p>Evet. Kendi karom seçeneğiyle JPG, PNG veya WebP karo görselinizi yükleyebilirsiniz.</p></details>
<details><summary><strong>Karo ebadı görselleştirmeyi etkiler mi?</strong></summary><p>Evet. Seçilen karo ebadı 2D yerleşim hesabında ve AI görselleştirme talimatında kullanılır.</p></details>
<details><summary><strong>AI sonucunu yeniden düzenleyebilir miyim?</strong></summary><p>Evet. Sonucu daha açık, daha koyu veya daha modern göstermek gibi yeni AI varyasyonları oluşturabilirsiniz.</p></details>
</section>
</main>
<x-site.contech-footer />

<script>
(() => {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const roomInput = document.getElementById('roomImage');
    const tileInput = document.getElementById('tileImage');
    const roomThumb = document.getElementById('roomThumb');
    const mainPreview = document.getElementById('mainPreview');
    const emptyState = document.getElementById('emptyState');
    const renderBtn = document.getElementById('renderBtn');
    const loading = document.getElementById('aiLoading');
    const errorBox = document.getElementById('errorBox');
    const customBox = document.getElementById('customBox');
    const canvas = document.getElementById('layoutCanvas');
    const ctx = canvas.getContext('2d');

    let state = {
        surface: 'floor',
        source: 'catalog',
        catalogTile: 'carrara-beyaz',
        tileSize: '60x120',
        tileName: 'Carrara Beyaz',
        roomFile: null,
        tileFile: null,
    };

    const setActive = (container, target) => {
        container.querySelectorAll('.chip').forEach(x => x.classList.toggle('active', x === target));
    };

    document.getElementById('surfaceChips').addEventListener('click', e => {
        const b = e.target.closest('[data-value]'); if (!b) return;
        setActive(e.currentTarget, b); state.surface = b.dataset.value;
    });

    document.getElementById('styleChips').addEventListener('click', e => {
        const b = e.target.closest('[data-style]'); if (!b) return;
        setActive(e.currentTarget, b);
        const style = b.dataset.style;
        const items = [...document.querySelectorAll('#catalogTiles .tile-item')];
        items.forEach(item => {
            item.hidden = style !== 'all' && item.dataset.style !== style;
        });

        const active = document.querySelector('#catalogTiles .tile.active');
        if (active && active.closest('.tile-item')?.hidden) {
            active.classList.remove('active');
        }

        if (!document.querySelector('#catalogTiles .tile.active')) {
            const firstVisible = document.querySelector('#catalogTiles .tile-item:not([hidden]) .tile');
            if (firstVisible) firstVisible.click();
        }
    });

    document.getElementById('sourceChips').addEventListener('click', e => {
        const b = e.target.closest('[data-value]'); if (!b) return;
        setActive(e.currentTarget, b); state.source = b.dataset.value;
        customBox.classList.toggle('active', state.source === 'custom');
    });

    const tileSizeSelect = document.getElementById('tileSizeSelect');
    const customTileSize = document.getElementById('customTileSize');
    const customTileWidth = document.getElementById('customTileWidth');
    const customTileHeight = document.getElementById('customTileHeight');

    function currentTileSize(){
        if(tileSizeSelect.value !== 'custom') return tileSizeSelect.value;
        const w = Math.max(2, Math.min(300, Number(customTileWidth.value) || 60));
        const h = Math.max(2, Math.min(300, Number(customTileHeight.value) || 120));
        return `${w}x${h}`;
    }

    function updateTilePreviewRatio(){
        const [w,h] = state.tileSize.split('x').map(Number);
        if(!w || !h) return;
        document.querySelectorAll('#catalogTiles .tile').forEach(tile => {
            const card = tile.closest('.tile-item');
            const available = Math.max(54, Math.min(88, (card?.clientWidth || 104) - 18));
            let pxW, pxH;
            if(w >= h){ pxW = available; pxH = available * (h / w); }
            else { pxH = available; pxW = available * (w / h); }
            tile.style.width = Math.max(9, Math.round(pxW)) + 'px';
            tile.style.height = Math.max(9, Math.round(pxH)) + 'px';
            tile.style.margin = '4px auto 0';
        });
    }
    function syncTileSize(){
        customTileSize.hidden = tileSizeSelect.value !== 'custom';
        state.tileSize = currentTileSize();
        updateTilePreviewRatio();
        drawPlan();
    }

    tileSizeSelect.addEventListener('change', syncTileSize);
    customTileWidth.addEventListener('input', syncTileSize);
    customTileHeight.addEventListener('input', syncTileSize);
    window.addEventListener('resize', updateTilePreviewRatio);

    document.getElementById('catalogTiles').addEventListener('click', e => {
        const b = e.target.closest('.tile'); if (!b) return;
        document.querySelectorAll('.tile').forEach(x => x.classList.toggle('active', x === b));
        state.catalogTile = b.dataset.key;
        state.tileName = b.dataset.name;
        updateTilePreviewRatio();
        drawPlan();
    });

    roomInput.addEventListener('change', () => {
        state.roomFile = roomInput.files[0] || null;
        if (!state.roomFile) return;
        const url = URL.createObjectURL(state.roomFile);
        roomThumb.src = url; roomThumb.style.display = 'block';
        mainPreview.src = url; emptyState.style.display = 'none';
        document.getElementById('watermark').style.display = 'none';
            document.getElementById('photoStage')?.classList.add('server-watermarked');
            document.getElementById('photoStage')?.classList.add('server-watermarked');
    });

    tileInput.addEventListener('change', () => { state.tileFile = tileInput.files[0] || null; });
    document.getElementById('surfaceWidth').addEventListener('input', drawPlan);
    document.getElementById('surfaceHeight').addEventListener('input', drawPlan);

    function getSurfaceDimensions(){
        const w=Number(document.getElementById('surfaceWidth').value);
        const h=Number(document.getElementById('surfaceHeight').value);
        return {
            w,h,
            valid:Number.isFinite(w) && Number.isFinite(h) && w>=0.5 && h>=0.5
        };
    }

    function drawPlan(){
        const dims=getSurfaceDimensions();

        if(!dims.valid){
            ctx.clearRect(0,0,canvas.width,canvas.height);
            ctx.fillStyle='#ffffff';
            ctx.fillRect(0,0,canvas.width,canvas.height);
            document.getElementById('statArea').textContent='—';
            document.getElementById('statCount').textContent='—';
            document.getElementById('planCount').textContent='Ölçü bekleniyor';
            document.getElementById('planMeta').textContent='Yüzey en ve boyunu girin.';
            return false;
        }


        const wM=dims.w;
        const hM=dims.h;
        const [twCm,thCm]=state.tileSize.split('x').map(Number);
        const tw=twCm/100, th=thCm/100;
        const cols=Math.ceil(wM/tw), rows=Math.ceil(hM/th);
        const count=cols*rows, area=wM*hM;
        const pad=26, cw=canvas.width-pad*2, ch=canvas.height-pad*2;
        const cellW=cw/cols, cellH=ch/rows;

        ctx.clearRect(0,0,canvas.width,canvas.height);
        ctx.fillStyle='#ffffff'; ctx.fillRect(0,0,canvas.width,canvas.height);
        ctx.fillStyle='#f2f4f7'; ctx.fillRect(pad,pad,cw,ch);
        ctx.strokeStyle='#98a2b3'; ctx.lineWidth=2;

        for(let x=0;x<=cols;x++){
            ctx.beginPath();
            ctx.moveTo(pad+x*cellW,pad);
            ctx.lineTo(pad+x*cellW,pad+ch);
            ctx.stroke();
        }

        for(let y=0;y<=rows;y++){
            ctx.beginPath();
            ctx.moveTo(pad,pad+y*cellH);
            ctx.lineTo(pad+cw,pad+y*cellH);
            ctx.stroke();
        }

        document.getElementById('statSize').textContent=state.tileSize.replace('x',' × ')+' cm';
        document.getElementById('statArea').textContent=area.toLocaleString('tr-TR',{minimumFractionDigits:2,maximumFractionDigits:2})+' m²';
        document.getElementById('statCount').textContent=count+' adet';
        document.getElementById('planCount').textContent=cols+' × '+rows+' = '+count+' karo';
        document.getElementById('planMeta').textContent=`${wM.toFixed(2)} × ${hM.toFixed(2)} m yüzeyde yaklaşık ${cols} sütun × ${rows} sıra. Kesim/fire dahil değildir.`;
        document.getElementById('costLink').href=`/araclar/seramik-maliyet-hesaplayici?tile_size=${encodeURIComponent(state.tileSize)}&source=visualizer`;
        return true;
    }

    renderBtn.addEventListener('click', async () => {
        errorBox.classList.remove('active'); errorBox.textContent='';
        if (!state.roomFile) return fail('Önce mekân fotoğrafınızı yükleyin.');
        if (state.source === 'custom' && !state.tileFile) return fail('Kendi seramiğiniz için karo görselini yükleyin.');
        const dims=getSurfaceDimensions();
        if(!dims.valid) return fail('Doğru karo ölçeği için yüzey genişliği ve uzunluğunu girin.');

        const fd = new FormData();
        fd.append('room_image', state.roomFile);
        fd.append('tile_source', state.source);
        fd.append('catalog_tile', state.catalogTile);
        if (state.tileFile) fd.append('tile_image', state.tileFile);
        fd.append('surface', state.surface);
        fd.append('tile_size', state.tileSize);
        fd.append('surface_width_m', dims.w);
        fd.append('surface_height_m', dims.h);
        if(window.tileEditPrompt){fd.append('user_prompt',window.tileEditPrompt);}

        loading.classList.add('active'); renderBtn.disabled = true;
        try{
            const res = await fetch('/api/seramik-gorsellestirici/render', {method:'POST',headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'},body:fd});
            const data = await res.json().catch(()=>({}));
            if(!res.ok){
                if(res.status===402 || data.payment_required){ showCreditPaywall(); return; }
                return fail(data.message || 'AI görselleştirme oluşturulamadı.');
            }
            mainPreview.src = data.image;
            const hdBtn=document.getElementById('hdDownloadLocked');
            if(hdBtn && data.download_url){hdBtn.dataset.url=data.download_url;hdBtn.textContent='⬇ Filigransız HD Sonucu İndir';hdBtn.classList.add('ready');}
            document.getElementById('watermark').style.display = 'none';
            document.getElementById('photoStage')?.classList.add('server-watermarked');
            document.getElementById('photoStage')?.classList.add('server-watermarked');
            document.getElementById('creditCount').textContent = data.credits;
            const testCreditCount=document.getElementById('testCreditCount'); if(testCreditCount) testCreditCount.textContent=data.credits;
            document.getElementById('statType').textContent = 'AI render + 2D';
            if(editPanel){editPanel.style.display='block';editPanel.scrollIntoView({behavior:'smooth',block:'nearest'});}
            if(window.tileEditPrompt){window.tileEditPrompt=''; if(editPrompt) editPrompt.value='';}
        }catch(e){ fail('Bağlantı hatası oluştu.'); }
        finally{ loading.classList.remove('active'); renderBtn.disabled = false; }
    });

    const editPanel=document.getElementById("editPanel"), editPrompt=document.getElementById("editPrompt"), editRenderBtn=document.getElementById("editRenderBtn");
    mainPreview.addEventListener("click",()=>{if(document.getElementById("photoStage")?.classList.contains("server-watermarked")) editPanel.style.display=editPanel.style.display==="none"?"block":"none";});
    editRenderBtn?.addEventListener("click",()=>{const p=editPrompt.value.trim();if(p.length<3)return fail("Düzenleme isteğinizi yazın.");window.tileEditPrompt=p;renderBtn.click();});
    /* PROMPT_CHIP_JS_V1 */ document.querySelectorAll("[data-edit-prompt]").forEach(b=>b.addEventListener("click",()=>{editPrompt.value=b.dataset.editPrompt;}));
    /* CREDIT_PAYWALL_JS_V1 */
    function showCreditPaywall(){const m=document.getElementById("CREDIT_PAYWALL_V1");if(m)m.style.display="flex";}
    document.getElementById("closeCreditPaywall")?.addEventListener("click",()=>{document.getElementById("CREDIT_PAYWALL_V1").style.display="none";});
    document.querySelectorAll("[data-credit-pack]").forEach(b=>b.addEventListener("click",()=>{document.getElementById("creditPackMsg").innerHTML="<strong>"+b.dataset.creditPack+" kredi seçildi.</strong> Ödeme adımı yakında aktif olacak."; }));
    function fail(message){ errorBox.textContent=message; errorBox.classList.add('active'); }
    syncTileSize();
})();
</script>
<script id="CATALOG_FILTER_V6">document.getElementById("styleChips")?.addEventListener("click",e=>{const b=e.target.closest("[data-style]");if(!b)return;document.querySelectorAll("#styleChips [data-style]").forEach(x=>x.classList.remove("active"));b.classList.add("active");const s=b.dataset.style;document.querySelectorAll("#catalogTiles .tile-item").forEach(x=>x.hidden=s!=="all"&&x.dataset.style!==s);});</script>

<style id="MOBILE_PREVIEW_V6">
/* MOBILE_PREVIEW_V6 */
.preview-expand,.preview-close{position:absolute;z-index:8;width:44px;height:44px;border:0;border-radius:999px;background:rgba(17,24,39,.82);color:#fff;font-size:22px;display:grid;place-items:center;cursor:pointer;box-shadow:0 4px 16px rgba(0,0,0,.22)}
.preview-expand{right:12px;bottom:12px}.preview-close{right:14px;top:14px;display:none}
#mainPreview{-webkit-user-select:none;user-select:none;-webkit-user-drag:none;touch-action:pinch-zoom;cursor:zoom-in}
.photo-stage:fullscreen,.photo-stage.preview-fullscreen{position:fixed!important;inset:0!important;width:100vw!important;height:100vh!important;height:100dvh!important;max-width:none!important;min-height:0!important;aspect-ratio:auto!important;border-radius:0!important;z-index:99999!important;background:#000!important;overflow:hidden!important}
.photo-stage:fullscreen img,.photo-stage.preview-fullscreen img{object-fit:contain!important;width:100%!important;height:100%!important;position:absolute!important;inset:0!important;touch-action:pinch-zoom!important}
.photo-stage:fullscreen .preview-close,.photo-stage.preview-fullscreen .preview-close{display:grid!important}
.photo-stage:fullscreen .preview-expand,.photo-stage.preview-fullscreen .preview-expand{display:none!important}
.btn-download-locked{background:#eef1f5;color:#344054;border:1px solid #d0d5dd;padding:0 16px}
.plan canvas{aspect-ratio:720/520!important;height:auto!important}
@media(max-width:720px){
  .preview-card{padding:12px!important}
  .photo-stage.image-ready{height:auto!important;min-height:0!important;aspect-ratio:var(--preview-ratio)!important;border-radius:14px!important}
  .photo-stage.image-ready img{object-fit:contain!important}
  .preview-grid{gap:12px!important}
  .plan{padding:12px!important;margin-top:0!important}
  .plan canvas{width:100%!important;height:auto!important;aspect-ratio:720/520!important}
  .plan-meta{font-size:12px!important;line-height:1.45!important;margin-top:12px!important}
  .stats{grid-template-columns:repeat(2,minmax(0,1fr))!important}
  .stat{min-width:0!important;padding:12px!important}
  .stat strong{font-size:15px!important;overflow-wrap:anywhere}
  .actions{grid-template-columns:1fr!important}
  .actions .btn,.actions a{width:100%!important}
}
@media(max-width:380px){.stats{grid-template-columns:1fr 1fr!important}.stat{padding:10px!important}.stat strong{font-size:14px!important}}
</style>
<script id="MOBILE_PREVIEW_V6_JS">
(() => {
  const stage=document.getElementById('photoStage');
  const img=document.getElementById('mainPreview');
  const openBtn=document.getElementById('previewExpandBtn');
  const closeBtn=document.getElementById('previewCloseBtn');
  if(!stage||!img) return;

  const syncRatio=()=>{
    if(img.naturalWidth>0&&img.naturalHeight>0){
      stage.style.setProperty('--preview-ratio',`${img.naturalWidth}/${img.naturalHeight}`);
      stage.classList.add('image-ready');
    }
  };
  img.addEventListener('load',syncRatio);
  if(img.complete) syncRatio();
  img.draggable=false;
  img.addEventListener('dragstart',e=>e.preventDefault());
  img.addEventListener('contextmenu',e=>e.preventDefault());

  const openPreview=()=>{
    if(!img.getAttribute('src')) return;
    if(stage.requestFullscreen){
      stage.requestFullscreen().catch(()=>stage.classList.add('preview-fullscreen'));
    }else stage.classList.add('preview-fullscreen');
  };
  const closePreview=()=>{
    if(document.fullscreenElement&&document.exitFullscreen) document.exitFullscreen();
    stage.classList.remove('preview-fullscreen');
  };
  openBtn?.addEventListener('click',e=>{e.stopPropagation();openPreview();});
  img.addEventListener('click',openPreview);
  closeBtn?.addEventListener('click',e=>{e.stopPropagation();closePreview();});
  document.addEventListener('fullscreenchange',()=>{if(!document.fullscreenElement) stage.classList.remove('preview-fullscreen');});

  document.getElementById('hdDownloadLocked')?.addEventListener('click',()=>{
    const btn=document.getElementById('hdDownloadLocked');
    const url=btn?.dataset.url;
    if(!url){alert('Önce 1 kredi ile AI render oluşturun. HD indirme aynı krediye dahildir.');return;}
    window.location.href=url;
  });
})();
</script>
<style id="FILTER_HIDDEN_FIX">#catalogTiles .tile-item[hidden]{display:none!important}</style>
<style id="WATERMARK_CENTER_V7">.watermark{display:flex;align-items:center;justify-content:center!important}.watermark span{position:absolute!important;left:50%!important;top:50%!important;width:max-content!important;max-width:none!important;white-space:nowrap!important;text-align:center!important;transform:translate(-50%,-50%) rotate(-18deg)!important;font-size:clamp(30px,6vw,72px)!important;line-height:1!important;letter-spacing:2px!important}.photo-stage:fullscreen .watermark span{font-size:clamp(48px,8vw,120px)!important}</style>

<!-- FUNNEL_WATERMARK_V3 -->
<style id="WATERMARK_IMAGE_CENTER_V8">
.photo-stage .watermark{
    inset:auto!important;
    display:flex!important;
    align-items:center!important;
    justify-content:center!important;
    overflow:hidden!important;
    pointer-events:none!important;
}
.photo-stage .watermark span{
    position:static!important;
    left:auto!important;top:auto!important;
    width:100%!important;
    max-width:100%!important;
    white-space:nowrap!important;
    text-align:center!important;
    transform:rotate(-16deg)!important;
    line-height:1!important;
    letter-spacing:1.5px!important;
}
</style>
<script id="FUNNEL_PARAMS_V3">
(function(){
  function applyFunnelParams(){
    const q=new URLSearchParams(location.search);
    const size=(q.get('tile_size')||'').trim().replace('×','x');
    const area=Number.parseFloat(q.get('area'));
    const s=document.getElementById('tileSizeSelect');
    const w=document.getElementById('surfaceWidth');
    const h=document.getElementById('surfaceHeight');

    if(size && s){
      const opt=[...s.options].find(o=>String(o.value).replace('×','x')===size);
      if(opt){
        s.value=opt.value;
        s.dispatchEvent(new Event('change',{bubbles:true}));
      }
    }

    const source=(q.get('source')||'').trim();
    const width=Number.parseFloat(q.get('surface_width_m'));
    const height=Number.parseFloat(q.get('surface_height_m'));
    const note=document.getElementById('dimensionSourceNote');

    if(Number.isFinite(width) && width>=0.5 && Number.isFinite(height) && height>=0.5 && w && h){
      w.value=width.toFixed(2);
      h.value=height.toFixed(2);

      if(note && source==='cost-calculator'){
        note.innerHTML='<strong>✓ Ölçüleriniz maliyet hesaplayıcıdan otomatik aktarıldı.</strong>';
      }

      w.dispatchEvent(new Event('input',{bubbles:true}));
      h.dispatchEvent(new Event('input',{bubbles:true}));
    }else if(note && source==='cost-calculator'){
      note.innerHTML='<strong>Alan ve karo ebadı aktarıldı.</strong> Hesaplamada en/boy bulunmadığı için yüzey ölçülerini girin.';
    }
  }

  function fitWatermark(){
    const stage=document.querySelector('.photo-stage');
    const img=stage?.querySelector('img');
    const wm=stage?.querySelector('.watermark');
    const text=wm?.querySelector('span');
    if(!stage || !img || !wm || !text) return;

    const sw=stage.clientWidth, sh=stage.clientHeight;
    if(!sw || !sh) return;
    let left=0, top=0, width=sw, height=sh;
    const nw=img.naturalWidth||0, nh=img.naturalHeight||0;
    const fit=getComputedStyle(img).objectFit || 'cover';

    if(nw>0 && nh>0 && fit==='contain'){
      const scale=Math.min(sw/nw,sh/nh);
      width=nw*scale; height=nh*scale;
      left=(sw-width)/2; top=(sh-height)/2;
    }

    Object.assign(wm.style,{
      left:left+'px',top:top+'px',width:width+'px',height:height+'px',right:'auto',bottom:'auto'
    });
    text.style.fontSize=Math.max(28,Math.min(96,width/10))+'px';
  }

  function boot(){
    applyFunnelParams();
    setTimeout(applyFunnelParams,120);
    setTimeout(applyFunnelParams,450);
    fitWatermark();
    setTimeout(fitWatermark,150);
    setTimeout(fitWatermark,600);
    const stage=document.querySelector('.photo-stage');
    const img=stage?.querySelector('img');
    img?.addEventListener('load',fitWatermark);
    window.addEventListener('resize',fitWatermark,{passive:true});
    document.addEventListener('fullscreenchange',()=>setTimeout(fitWatermark,50));
    if(stage && 'ResizeObserver' in window) new ResizeObserver(fitWatermark).observe(stage);
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',boot,{once:true}); else boot();
})();
</script>
<style id="PAID_PREVIEW_UI_V7">
/* PAID_PREVIEW_UI_V7 */
.plan-count{margin-top:10px;padding:9px 10px;border-radius:9px;background:#eef2f6;color:#101828;font-size:14px;line-height:1.35;font-weight:900;text-align:left}
.photo-stage.server-watermarked .watermark{display:none!important}
.btn-download-locked.ready{background:#ffb800!important;color:#111!important;border-color:#ffb800!important;cursor:pointer!important}
@media(max-width:720px){.plan-count{font-size:13px;margin-top:8px}.plan-meta{margin-top:8px!important}}
</style>

<style id="SERVER_WATERMARK_ONLY_V9">.watermark{display:none!important}</style>
</body>
</html>
