/*
 * Worksitem GES planlayıcı — çatı geometrisi ve panel yerleşimi.
 * Plan koordinatları metre: x = doğu, y = kuzey (bina merkezine göre).
 * Tarayıcıda window.GesGeo, Node'da module.exports.
 */
(function (root) {
    'use strict';
    var D = Math.PI / 180;

    function project(ring) {   // [[lat,lon],...] → {origin, pts:[[x,y]]}
        var lat0 = 0, lon0 = 0;
        ring.forEach(function (p) { lat0 += p[0]; lon0 += p[1]; });
        lat0 /= ring.length; lon0 /= ring.length;
        var kx = 111320 * Math.cos(lat0 * D), ky = 110540;
        return { origin: [lat0, lon0], kx: kx, ky: ky, pts: ring.map(function (p) { return [(p[1] - lon0) * kx, (p[0] - lat0) * ky]; }) };
    }
    function unproject(pr, x, y) { return [pr.origin[0] + y / pr.ky, pr.origin[1] + x / pr.kx]; }

    function area(pts) { var a = 0; for (var i = 0, j = pts.length - 1; i < pts.length; j = i++) a += (pts[j][0] + pts[i][0]) * (pts[j][1] - pts[i][1]); return Math.abs(a) / 2; }
    function inside(x, y, pts) {
        var c = false;
        for (var i = 0, j = pts.length - 1; i < pts.length; j = i++) {
            var xi = pts[i][0], yi = pts[i][1], xj = pts[j][0], yj = pts[j][1];
            if (((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / ((yj - yi) || 1e-12) + xi)) c = !c;
        }
        return c;
    }
    function segDist(px, py, ax, ay, bx, by) {
        var dx = bx - ax, dy = by - ay, l = dx * dx + dy * dy, t = l ? ((px - ax) * dx + (py - ay) * dy) / l : 0;
        t = Math.max(0, Math.min(1, t));
        return Math.hypot(px - ax - t * dx, py - ay - t * dy);
    }
    function edgeDist(x, y, pts) {
        var m = Infinity;
        for (var i = 0, j = pts.length - 1; i < pts.length; j = i++) m = Math.min(m, segDist(x, y, pts[j][0], pts[j][1], pts[i][0], pts[i][1]));
        return m;
    }

    /** En küçük alanlı yönlü sınır kutusu: {angle (u ekseni, rad), cx, cy, L (u boyu ≥), W} */
    function obb(pts) {
        var best = null;
        for (var i = 0; i < pts.length; i++) {
            var a = pts[i], b = pts[(i + 1) % pts.length], th = Math.atan2(b[1] - a[1], b[0] - a[0]);
            var c = Math.cos(th), s = Math.sin(th), mnU = Infinity, mxU = -Infinity, mnV = Infinity, mxV = -Infinity;
            pts.forEach(function (p) { var u = p[0] * c + p[1] * s, v = -p[0] * s + p[1] * c; mnU = Math.min(mnU, u); mxU = Math.max(mxU, u); mnV = Math.min(mnV, v); mxV = Math.max(mxV, v); });
            var ar = (mxU - mnU) * (mxV - mnV);
            if (!best || ar < best.ar - 1e-6) best = { ar: ar, th: th, mnU: mnU, mxU: mxU, mnV: mnV, mxV: mxV };
        }
        var th2 = best.th, L = best.mxU - best.mnU, W = best.mxV - best.mnV;
        var cu = (best.mnU + best.mxU) / 2, cv = (best.mnV + best.mxV) / 2;
        var c2 = Math.cos(th2), s2 = Math.sin(th2);
        var cx = cu * c2 - cv * s2, cy = cu * s2 + cv * c2;
        if (W > L) { th2 += Math.PI / 2; var t = L; L = W; W = t; }
        return { angle: th2, cx: cx, cy: cy, L: L, W: W };
    }

    /** Yarım düzleme kırpma (Sutherland–Hodgman): n·p ≤ k tarafı kalır */
    function clip(pts, nx, ny, k) {
        var out = [];
        for (var i = 0; i < pts.length; i++) {
            var A = pts[i], B = pts[(i + 1) % pts.length];
            var da = nx * A[0] + ny * A[1] - k, db = nx * B[0] + ny * B[1] - k;
            if (da <= 0) out.push(A);
            if ((da <= 0) !== (db <= 0)) { var t = da / (da - db); out.push([A[0] + t * (B[0] - A[0]), A[1] + t * (B[1] - A[1])]); }
        }
        return out;
    }

    function compass(dx, dy) { var a = Math.atan2(dx, dy) / D; return (a + 360) % 360; }   // 0=K, 90=D
    var DIRS = ['Kuzey', 'Kuzeydoğu', 'Doğu', 'Güneydoğu', 'Güney', 'Güneybatı', 'Batı', 'Kuzeybatı'];
    function dirName(az) { return DIRS[Math.round(az / 45) % 8]; }

    /**
     * Çatı yüzeyleri.
     * opts: {type: 'duz'|'besik'|'kirma'|'trapez', slope (°), swap (mahya kısa kenara), wallH}
     * Yüzey: {id, pts, slope, azimuth, down:[dx,dy] (eğim aşağı yönü, plan), h(x,y) → yükseklik}
     */
    function faces(pts, opts) {
        var o = obb(pts), th = o.angle + (opts.swap ? Math.PI / 2 : 0);
        var L = opts.swap ? o.W : o.L, W = opts.swap ? o.L : o.W;
        var ux = Math.cos(th), uy = Math.sin(th), vx = -uy, vy = ux;   // u: mahya yönü, v: dik
        var t = Math.tan((opts.type === 'duz' ? 0 : opts.slope) * D), H = opts.wallH;
        var U = function (x, y) { return (x - o.cx) * ux + (y - o.cy) * uy; }, V = function (x, y) { return (x - o.cx) * vx + (y - o.cy) * vy; };
        // yerel kırpma: a·U + b·V ≤ k
        function cl(poly, a, b, k) { var nx = a * ux + b * vx, ny = a * uy + b * vy; return clip(poly, nx, ny, k + nx * o.cx + ny * o.cy); }
        var list = [];
        function add(poly, down, hf) {
            if (poly.length >= 3 && area(poly) > 1) list.push({ pts: poly, slope: opts.type === 'duz' ? 0 : opts.slope, down: down, azimuth: compass(down[0], down[1]), h: hf });
        }
        if (opts.type === 'duz') {
            add(pts.slice(), [0, -1], function () { return H; });
        } else if (opts.type === 'kirma' && L - W > 0.5) {
            var e = (L - W) / 2;
            add(cl(cl(cl(pts, 0, -1, 0), 1, -1, e), -1, -1, e), [vx, vy], function (x, y) { return H + (W / 2 - V(x, y)) * t; });
            add(cl(cl(cl(pts, 0, 1, 0), 1, 1, e), -1, 1, e), [-vx, -vy], function (x, y) { return H + (W / 2 + V(x, y)) * t; });
            add(cl(cl(pts, -1, 1, -e), -1, -1, -e), [ux, uy], function (x, y) { return H + (L / 2 - U(x, y)) * t; });
            add(cl(cl(pts, 1, 1, -e), 1, -1, -e), [-ux, -uy], function (x, y) { return H + (L / 2 + U(x, y)) * t; });
        } else {
            // beşik ve trapez (kırma için uzun kenar yoksa beşik gibi): mahya boyunca ikiye
            add(cl(pts, 0, -1, 0), [vx, vy], function (x, y) { return H + (W / 2 - V(x, y)) * t; });
            add(cl(pts, 0, 1, 0), [-vx, -vy], function (x, y) { return H + (W / 2 + V(x, y)) * t; });
        }
        list.forEach(function (f, i) { f.id = i + 1; f.dir = dirName(f.azimuth); });
        var gable = opts.type === 'besik' || opts.type === 'trapez' || (opts.type === 'kirma' && L - W <= 0.5);
        // Duvar üst kotu (beşikte alın duvarı mahyaya kadar yükselir) ve mahya kesişimleri eklenmiş dış hat
        function wallTop(x, y) { return gable ? H + Math.max(0, W / 2 - Math.abs(V(x, y))) * t : H; }
        var ring = [];
        pts.forEach(function (a, i) {
            var b = pts[(i + 1) % pts.length], va = V(a[0], a[1]), vb = V(b[0], b[1]);
            ring.push(a);
            if (gable && va * vb < 0) { var k = va / (va - vb); ring.push([a[0] + k * (b[0] - a[0]), a[1] + k * (b[1] - a[1])]); }
        });
        return { faces: list, obb: o, ridge: { ux: ux, uy: uy, L: L, W: W }, ridgeH: H + W / 2 * t, wallTop: wallTop, wallRing: ring };
    }

    /**
     * Panel yerleşimi.
     * face: faces() çıktısı; p: {l, s, w (panel), setback, gap, flat:{tilt, lat}} ; düz çatıda sıralar güneye bakar.
     * Dönen: {panels:[{c:[x,y], corners:[[x,y]x4], az, tilt, h}], tilt, azimuth}
     */
    function layout(face, p) {
        var gap = p.gap || 0.02, sb = p.setback || 0.5, flat = face.slope === 0;
        var dx = face.down[0], dy = face.down[1], tilt = face.slope, az = face.azimuth;
        if (flat) {   // sıraları bina eksenine hizala, güneye en yakın yöne baktır
            var o = obb(face.pts), best = null, bd = 999;
            [0, 90, 180, 270].forEach(function (k) {
                var a = o.angle + k * D, c = [Math.cos(a), Math.sin(a)], d = Math.abs(180 - compass(c[0], c[1]));
                if (d < bd) { bd = d; best = c; }
            });
            dx = best[0]; dy = best[1]; tilt = p.flat.tilt; az = compass(dx, dy);
            if (p.south) { dx = 0; dy = -1; az = 180; }   // arazi: sıralar parsel yönünden bağımsız tam güneye
        }
        var ex = -dy, ey = dx;                          // saçak boyunca
        var cosT = Math.cos(tilt * D), sinT = Math.sin(tilt * D);
        var wPlan = p.s + gap;                          // dikey (portre) yerleşim: kısa kenar saçak boyunca
        var dPanel = p.l * cosT;                        // panelin plandaki derinliği
        var rowPitch = dPanel + gap;
        if (flat) {   // kış gündönümü öğlen gölgesi kadar sıra aralığı
            var elev = Math.max(12, 90 - p.flat.lat - 23.45) * D;
            rowPitch = dPanel + p.l * sinT / Math.tan(elev) + 0.1;
        }
        var A = face.pts.map(function (q) { return q[0] * ex + q[1] * ey; }), B = face.pts.map(function (q) { return -(q[0] * dx + q[1] * dy); });
        var a0 = Math.min.apply(null, A), a1 = Math.max.apply(null, A), b0 = Math.min.apply(null, B), b1 = Math.max.apply(null, B);
        function toXY(a, b) { return [a * ex - b * dx, a * ey - b * dy]; }
        function fits(a, b) {
            var cs = [[a, b], [a + p.s, b], [a + p.s, b + dPanel], [a, b + dPanel]].map(function (c) { return toXY(c[0], c[1]); });
            for (var i = 0; i < 4; i++) if (!inside(cs[i][0], cs[i][1], face.pts) || edgeDist(cs[i][0], cs[i][1], face.pts) < sb) return null;
            return cs;
        }
        var bestRun = null;
        var big = (a1 - a0) * (b1 - b0) > 20000;          // büyük parselde daha az kaydırma denemesi (hız)
        (big ? [0, 0.5] : [0, 0.25, 0.5, 0.75]).forEach(function (fa) {
            (big ? [0, 0.5] : [0, 0.15, 0.33, 0.66]).forEach(function (fb) {
                var run = [];
                for (var b = b0 + sb + fb * rowPitch; b + dPanel <= b1 + 1e-6; b += rowPitch)
                    for (var a = a0 + sb + fa * wPlan; a + p.s <= a1 + 1e-6; a += wPlan) { var cs = fits(a, b); if (cs) run.push(cs); }
                if (!bestRun || run.length > bestRun.length) bestRun = run;
            });
        });
        var panels = (bestRun || []).map(function (cs) {
            var cx = (cs[0][0] + cs[2][0]) / 2, cy = (cs[0][1] + cs[2][1]) / 2;
            return { c: [cx, cy], corners: cs, h: face.h(cx, cy) };
        });
        return { panels: panels, tilt: tilt, azimuth: az, down: [dx, dy], kwp: panels.length * p.w / 1000 };
    }

    function edges(pts) {   // [{a:[x,y], b:[x,y], len}]
        return pts.map(function (a, i) { var b = pts[(i + 1) % pts.length]; return { a: a, b: b, len: Math.hypot(b[0] - a[0], b[1] - a[1]) }; });
    }

    var api = { project: project, unproject: unproject, area: area, inside: inside, obb: obb, clip: clip, faces: faces, layout: layout, edges: edges, compass: compass, dirName: dirName };
    if (typeof module === 'object' && module.exports) module.exports = api; else root.GesGeo = api;
})(typeof window !== 'undefined' ? window : this);
