/*
 * GES planlayıcı — arazi / parsel yardımcıları (v89). Hepsi tarayıcıda çalışır; dosya sunucuya gönderilmez.
 *  - coords(text): "40.98, 29.03", "40.98 29.03", Google Maps bağlantısı (@lat,lon · q=lat,lon · !3dlat!4dlon),
 *    derece-dakika-saniye (40°58'48"N 29°01'48"E) → {lat, lon} ya da null
 *  - parseFile(File|ArrayBuffer, name): KML, KMZ (zip), GeoJSON → {ring:[[lat,lon],...], name, area?} (en büyük çokgen)
 *  - Koordinatların Türkiye içinde ve WGS84 (enlem/boylam) olması beklenir; projeksiyonlu (metre) değerler reddedilir.
 */
(function (root) {
    'use strict';

    var TR = { latMin: 35, latMax: 43, lonMin: 25, lonMax: 45 };
    function inTR(lat, lon) { return lat >= TR.latMin && lat <= TR.latMax && lon >= TR.lonMin && lon <= TR.lonMax; }

    function err(msg) { var e = new Error(msg); e.userMessage = msg; return e; }

    /* ── Koordinat / bağlantı ───────────────────────────────────────────── */
    function dms(s) {
        var re = /(\d{1,3})\s*[°º]\s*(\d{1,2})\s*['’′]\s*(\d{1,2}(?:[.,]\d+)?)\s*(?:"|”|″|'')?\s*([NSEWKGDB])/gi, m, out = [];
        while ((m = re.exec(s))) {
            var v = +m[1] + +m[2] / 60 + parseFloat(m[3].replace(',', '.')) / 3600, h = m[4].toUpperCase();
            if (h === 'S' || h === 'W' || h === 'G' || h === 'B') v = -v;   // G: güney, B: batı
            out.push({ v: v, lat: /[NSKG]/.test(h) });
        }
        if (out.length !== 2) return null;
        var la = out[0].lat ? out[0].v : out[1].v, lo = out[0].lat ? out[1].v : out[0].v;
        return { lat: la, lon: lo };
    }

    function coords(text) {
        var s = String(text || '').trim();
        if (!s) return null;
        var m;
        // Google Maps: önce yer işareti (!3d..!4d..), sonra q= / ll= / query=, sonra @lat,lon
        if ((m = s.match(/!3d(-?\d+(?:\.\d+)?)!4d(-?\d+(?:\.\d+)?)/))) return ok(+m[1], +m[2]);
        if ((m = s.match(/[?&](?:q|ll|query|center|destination)=(-?\d+(?:\.\d+)?)(?:,|%2C)\s*(-?\d+(?:\.\d+)?)/i))) return ok(+m[1], +m[2]);
        if ((m = s.match(/@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/))) return ok(+m[1], +m[2]);
        var d = dms(s);
        if (d) return ok(d.lat, d.lon);
        // Düz sayı çifti: "40.98, 29.03" · "40.98 29.03" · "40,98 29,03" (Türkçe ondalık virgül, boşlukla ayrılmış)
        if ((m = s.match(/^(-?\d{1,2}\.\d+)\s*[,;\s]\s*(-?\d{1,3}\.\d+)$/))) return ok(+m[1], +m[2]);
        if ((m = s.match(/^(-?\d{1,2},\d+)\s*[;\s]\s*(-?\d{1,3},\d+)$/))) return ok(+m[1].replace(',', '.'), +m[2].replace(',', '.'));
        return null;

        function ok(a, b) {
            if (inTR(a, b)) return { lat: a, lon: b };
            if (inTR(b, a)) return { lat: b, lon: a };   // boylam, enlem sırasıyla yazılmış
            return null;
        }
    }

    /* ── Çokgen yardımcıları ───────────────────────────────────────────── */
    function areaLL(ring) {   // yaklaşık m² (yalnız en büyüğü seçmek için)
        var lat0 = 0; ring.forEach(function (p) { lat0 += p[0]; }); lat0 /= ring.length;
        var kx = 111320 * Math.cos(lat0 * Math.PI / 180), ky = 110540, a = 0;
        for (var i = 0, j = ring.length - 1; i < ring.length; j = i++) a += (ring[j][1] * kx + ring[i][1] * kx) * (ring[j][0] * ky - ring[i][0] * ky);
        return Math.abs(a) / 2;
    }

    function cleanRing(lonLat) {   // [[lon,lat],...] → [[lat,lon],...] kapanış noktası olmadan
        var r = lonLat.filter(function (p) { return p && isFinite(p[0]) && isFinite(p[1]); }).map(function (p) { return [+p[1], +p[0]]; });
        if (r.length > 1 && r[0][0] === r[r.length - 1][0] && r[0][1] === r[r.length - 1][1]) r.pop();
        return r;
    }

    function pickLargest(rings) {
        rings = rings.filter(function (r) { return r.length >= 3; });
        if (!rings.length) throw err('Dosyada çokgen (parsel sınırı) bulunamadı.');
        var bad = rings.filter(function (r) { return r.some(function (p) { return Math.abs(p[0]) > 90 || Math.abs(p[1]) > 180; }); });
        if (bad.length === rings.length) throw err('Koordinatlar enlem/boylam değil (metre cinsinden projeksiyon olabilir). Dosyayı WGS84 (EPSG:4326) olarak dışa aktarın.');
        rings = rings.filter(function (r) { return bad.indexOf(r) === -1; });
        var inside = rings.filter(function (r) { return r.every(function (p) { return inTR(p[0], p[1]); }); });
        if (!inside.length) throw err('Parsel Türkiye sınırları içinde görünmüyor; enlem ve boylam yer değiştirmiş olabilir.');
        inside.sort(function (a, b) { return areaLL(b) - areaLL(a); });
        if (inside[0].length > 2000) throw err('Sınır çok ayrıntılı (2.000 noktadan fazla). Lütfen yalnız parsel sınırını içeren bir dosya yükleyin.');
        return inside[0];
    }

    /* ── GeoJSON ───────────────────────────────────────────────────────── */
    function geojsonRings(g, out, names) {
        if (!g || typeof g !== 'object') return;
        if (g.type === 'FeatureCollection') (g.features || []).forEach(function (f) { geojsonRings(f, out, names); });
        else if (g.type === 'Feature') {
            var before = out.length;
            geojsonRings(g.geometry, out, names);
            var p = g.properties || {}, nm = p.name || p.Name || p.ad || ((p.AdaNo || p.adaNo || p.ada) ? ('Ada ' + (p.AdaNo || p.adaNo || p.ada) + ' Parsel ' + (p.ParselNo || p.parselNo || p.parsel || '')) : '');
            for (var i = before; i < out.length; i++) names[i] = nm;
        } else if (g.type === 'GeometryCollection') (g.geometries || []).forEach(function (x) { geojsonRings(x, out, names); });
        else if (g.type === 'Polygon' && g.coordinates) out.push(cleanRing(g.coordinates[0] || []));
        else if (g.type === 'MultiPolygon' && g.coordinates) g.coordinates.forEach(function (poly) { out.push(cleanRing(poly[0] || [])); });
        else if (g.type === 'LineString' && g.coordinates && g.coordinates.length > 3) out.push(cleanRing(g.coordinates));
    }

    function fromGeoJSON(text) {
        var g;
        try { g = JSON.parse(text); } catch (e) { throw err('GeoJSON okunamadı (geçersiz JSON).'); }
        if (g && g.crs && g.crs.properties && /3857|900913|5253|5254|5255|5256|2320|2321|2322|2323|2324|2325|2326|2327/.test(String(g.crs.properties.name || ''))) {
            throw err('Dosya metre cinsinden projeksiyonda (' + g.crs.properties.name + '). WGS84 (EPSG:4326) olarak dışa aktarın.');
        }
        var rings = [], names = {};
        geojsonRings(g, rings, names);
        var ring = pickLargest(rings);
        return { ring: ring, name: names[rings.indexOf(ring)] || '' };
    }

    /* ── KML ───────────────────────────────────────────────────────────── */
    function kmlCoords(t) {
        return String(t || '').trim().split(/\s+/).map(function (c) { var a = c.split(','); return [parseFloat(a[0]), parseFloat(a[1])]; });
    }

    function fromKML(text) {
        var doc = new DOMParser().parseFromString(text, 'application/xml');
        if (doc.getElementsByTagName('parsererror').length) throw err('KML dosyası okunamadı.');
        var rings = [], names = {};
        var marks = doc.getElementsByTagNameNS('*', 'Placemark');
        var scan = function (node, nm) {
            var polys = node.getElementsByTagNameNS('*', 'Polygon');
            for (var i = 0; i < polys.length; i++) {
                var ob = polys[i].getElementsByTagNameNS('*', 'outerBoundaryIs')[0] || polys[i];
                var c = ob.getElementsByTagNameNS('*', 'coordinates')[0];
                if (c) { names[rings.length] = nm; rings.push(cleanRing(kmlCoords(c.textContent))); }
            }
            if (!polys.length) {   // kapalı çizgi (LinearRing / LineString) olarak çizilmiş sınır
                var lines = node.getElementsByTagNameNS('*', 'LineString');
                for (var k = 0; k < lines.length; k++) {
                    var lc = lines[k].getElementsByTagNameNS('*', 'coordinates')[0];
                    if (lc) { var r = cleanRing(kmlCoords(lc.textContent)); if (r.length >= 3) { names[rings.length] = nm; rings.push(r); } }
                }
            }
        };
        if (marks.length) {
            for (var i = 0; i < marks.length; i++) {
                var n = marks[i].getElementsByTagNameNS('*', 'name')[0];
                scan(marks[i], n ? n.textContent.trim() : '');
            }
        } else scan(doc, '');
        var ring = pickLargest(rings);
        return { ring: ring, name: names[rings.indexOf(ring)] || '' };
    }

    /* ── KMZ (zip) — yalnız ilk .kml dosyası; "deflate" ya da sıkıştırmasız ── */
    function u16(v, o) { return v.getUint16(o, true); }
    function u32(v, o) { return v.getUint32(o, true); }

    function unzipFirstKml(buf) {
        var v = new DataView(buf), n = buf.byteLength, eocd = -1;
        for (var i = n - 22; i >= Math.max(0, n - 65557); i--) { if (u32(v, i) === 0x06054b50) { eocd = i; break; } }
        if (eocd < 0) return Promise.reject(err('KMZ dosyası açılamadı (zip değil).'));
        var count = u16(v, eocd + 10), off = u32(v, eocd + 16);
        for (var e = 0; e < count; e++) {
            if (u32(v, off) !== 0x02014b50) break;
            var method = u16(v, off + 10), csize = u32(v, off + 20), nlen = u16(v, off + 28), xlen = u16(v, off + 30), clen = u16(v, off + 32), lho = u32(v, off + 42);
            var name = new TextDecoder().decode(new Uint8Array(buf, off + 46, nlen));
            off += 46 + nlen + xlen + clen;
            if (!/\.kml$/i.test(name)) continue;
            var start = lho + 30 + u16(v, lho + 26) + u16(v, lho + 28), data = new Uint8Array(buf, start, csize);
            if (method === 0) return Promise.resolve(new TextDecoder().decode(data));
            if (method !== 8) return Promise.reject(err('KMZ sıkıştırma biçimi desteklenmiyor.'));
            if (typeof DecompressionStream === 'undefined') return Promise.reject(err('Tarayıcınız KMZ açamıyor; dosyayı KML olarak kaydedip yükleyin.'));
            var ds = new Blob([data]).stream().pipeThrough(new DecompressionStream('deflate-raw'));
            return new Response(ds).text();
        }
        return Promise.reject(err('KMZ içinde KML dosyası bulunamadı.'));
    }

    /* ── Dosya ─────────────────────────────────────────────────────────── */
    function readBuf(file) {
        if (file instanceof ArrayBuffer) return Promise.resolve(file);
        if (file && typeof file.arrayBuffer === 'function') return file.arrayBuffer();
        return new Promise(function (res, rej) { var r = new FileReader(); r.onload = function () { res(r.result); }; r.onerror = function () { rej(err('Dosya okunamadı.')); }; r.readAsArrayBuffer(file); });
    }

    function parseFile(file, name) {
        name = String(name || (file && file.name) || '').toLowerCase();
        if (file && file.size > 5 * 1024 * 1024) return Promise.reject(err('Dosya çok büyük (en çok 5 MB).'));
        return readBuf(file).then(function (buf) {
            var head = new Uint8Array(buf, 0, Math.min(4, buf.byteLength));
            var isZip = head[0] === 0x50 && head[1] === 0x4b;
            if (isZip || /\.kmz$/.test(name)) return unzipFirstKml(buf).then(fromKML);
            var text = new TextDecoder().decode(buf).replace(/^﻿/, '').trim();
            if (/\.(geo)?json$/.test(name) || text.charAt(0) === '{') return fromGeoJSON(text);
            if (/\.kml$/.test(name) || text.charAt(0) === '<') return fromKML(text);
            throw err('Desteklenen dosyalar: KML, KMZ, GeoJSON.');
        });
    }

    var api = { coords: coords, parseFile: parseFile, fromKML: fromKML, fromGeoJSON: fromGeoJSON, inTR: inTR };
    if (typeof module !== 'undefined' && module.exports) module.exports = api;
    root.GesParcel = api;
})(typeof window !== 'undefined' ? window : globalThis);
