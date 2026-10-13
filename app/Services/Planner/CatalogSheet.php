<?php

namespace App\Services\Planner;

use App\Models\PlannerProduct;
use App\Models\PlannerTile;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Sponsor ürün / seramik kataloğu Excel şablonu ve içe aktarma.
 * Ölçüler şablonda mm (üretici dilinde), sistemde cm tutulur.
 * Görsel ve 3D model dosyaları admin'den ürün kaydına ayrıca yüklenir; şablonda yalnızca dosya adları yazılır.
 */
class CatalogSheet
{
    public const PRODUCT_TYPES = [
        'Duşakabin' => 'kabin', 'Küvet' => 'kuvet', 'Gömme duş' => 'gomme',
        'Lavabo' => 'lavabo', 'Klozet' => 'klozet', 'Havlupan' => 'havlupan',
    ];

    public const PRODUCT_COLS = [
        'Tür', 'Ürün adı', 'Marka', 'Stok kodu', 'Genişlik (mm)', 'Derinlik (mm)', 'Yükseklik (mm)',
        'Genişlik en az (mm)', 'Genişlik en çok (mm)', 'Derinlik en az (mm)', 'Derinlik en çok (mm)',
        'Renk (HEX)', 'Dolap rengi (HEX)', 'Rezervuarlı', 'Fiyat en az (TL, KDV dahil)', 'Fiyat en çok (TL, KDV dahil)',
        'Ürün sayfası (https)', 'Görsel dosya adı', '3D model dosya adı (GLB)',
    ];

    public const TILE_COLS = [
        'Ürün adı', 'Marka', 'Seri', 'Stok kodu', 'En (mm)', 'Boy (mm)', 'Yüzey', 'Kullanım',
        'm² fiyatı en az (TL, KDV dahil)', 'm² fiyatı en çok (TL, KDV dahil)', 'Ürün sayfası (https)', 'Doku görseli dosya adı',
    ];

    public function template(): Spreadsheet
    {
        $book = new Spreadsheet();
        $book->getProperties()->setTitle('Worksitem sponsor ürün şablonu')->setCreator('Worksitem');

        $info = $book->getActiveSheet()->setTitle('Nasıl doldurulur');
        $lines = [
            ['Worksitem 2D / 3D banyo planlayıcı · Sponsor ürün şablonu'],
            [''],
            ['1. "Ürünler" sayfasına her stok kodu için bir satır yazın; "Seramikler" sayfasına her karo için bir satır.'],
            ['2. Ölçüleri milimetre (mm) yazın. Ölçüsü ayarlanabilen ürünlerde (örn. duşakabin) en az / en çok değerleri doldurun; sabitse boş bırakın.'],
            ['3. Fiyatlar KDV dahil liste fiyatıdır (TL). Fiyat paylaşmak istemezseniz boş bırakın.'],
            ['4. Görselleri ve 3D modelleri ayrı bir klasörde gönderin; dosya adlarını ilgili sütuna aynen yazın.'],
            ['5. 3D model: GLB (glTF 2.0), gerçek ölçekte (metre), ön yüz +Z, en fazla 5 MB. GLB yoksa RFA, IFC, FBX, OBJ veya SKP kabul edilir; dönüştürmeyi Worksitem yapar.'],
            ['6. Seramik dokusu: tek karonun gerçek oranında, düz (perspektifsiz), tekrar ettiğinde dikiş görünmeyen JPG/PNG; uzun kenar en az 1024 piksel.'],
            ['7. "Tür" ve "Kullanım" sütunlarında açılır listeden seçin.'],
        ];
        foreach ($lines as $i => $l) {
            $info->setCellValue('A'.($i + 1), $l[0]);
        }
        $info->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $info->getColumnDimension('A')->setWidth(130);

        $this->sheet($book, 'Ürünler', self::PRODUCT_COLS, [
            ['Lavabo', 'Dolaplı lavabo 80 cm, beyaz', 'Örnek Marka', 'LV-80-W', 800, 460, 850, 600, 1000, '', '', '#FFFFFF', '#B08A5B', 'Hayır', 14500, 18900, 'https://ornek.com/lavabo-80', 'LV-80-W.jpg', 'LV-80-W.glb'],
        ], 'A', array_keys(self::PRODUCT_TYPES), 'N', ['Evet', 'Hayır']);
        $this->sheet($book, 'Seramikler', self::TILE_COLS, [
            ['Calacatta Mat', 'Örnek Marka', 'Marmo', 'CM-60120', 600, 1200, 'Mat', 'İkisi', 950, 1250, 'https://ornek.com/calacatta', 'CM-60120.jpg'],
        ], 'H', ['Zemin', 'Duvar', 'İkisi'], 'G', ['Mat', 'Parlak', 'Yarı mat', 'Rölyefli']);
        $book->setActiveSheetIndex(1);

        return $book;
    }

    private function sheet(Spreadsheet $book, string $title, array $cols, array $example, string $listCol, array $list, string $listCol2, array $list2): void
    {
        $s = $book->createSheet()->setTitle($title);
        foreach ($cols as $i => $c) {
            $s->setCellValue([$i + 1, 1], $c);
            $s->getColumnDimensionByColumn($i + 1)->setWidth(max(14, mb_strlen($c) + 4));
        }
        foreach ($example as $r => $row) {
            foreach ($row as $i => $v) {
                $s->setCellValue([$i + 1, $r + 2], $v);
            }
        }
        $last = $s->getHighestColumn();
        $s->getStyle("A1:{$last}1")->getFont()->setBold(true);
        $s->getStyle("A1:{$last}1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFB800');
        $s->getStyle("A2:{$last}2")->getFont()->setItalic(true)->getColor()->setRGB('5B6472');
        $s->freezePane('A2');
        foreach ([[$listCol, $list], [$listCol2, $list2]] as [$col, $opts]) {
            for ($r = 2; $r <= 500; $r++) {
                $v = $s->getCell($col.$r)->getDataValidation();
                $v->setType(DataValidation::TYPE_LIST)->setAllowBlank(true)->setShowDropDown(true)->setShowErrorMessage(true)
                    ->setFormula1('"'.implode(',', $opts).'"');
            }
        }
    }

    /** @return array{products:int, tiles:int, skipped:int, errors:array<int,string>} */
    public function import(string $path): array
    {
        $book = IOFactory::load($path);
        $res = ['products' => 0, 'tiles' => 0, 'skipped' => 0, 'errors' => []];
        $mm = fn ($v) => is_numeric($v) && $v > 0 ? (int) round($v / 10) : null;
        $num = fn ($v) => is_numeric($v) && $v > 0 ? (int) round($v) : null;
        $hex = fn ($v) => is_string($v) && preg_match('/^#?[0-9a-fA-F]{6}$/', trim($v)) ? '#'.strtoupper(ltrim(trim($v), '#')) : null;
        $url = fn ($v) => is_string($v) && preg_match('#^https://#i', trim($v)) ? trim($v) : null;

        if ($s = $book->getSheetByName('Ürünler')) {
            foreach ($s->toArray(null, true, false, false) as $i => $r) {
                if ($i === 0 || ! array_filter($r, fn ($v) => $v !== null && $v !== '')) {
                    continue;
                }
                $type = self::PRODUCT_TYPES[trim((string) $r[0])] ?? null;
                [$w, $d, $h] = [$mm($r[4]), $mm($r[5]), $mm($r[6])];
                $name = trim((string) $r[1]);
                $brand = trim((string) $r[2]);
                if ($brand === 'Örnek Marka') {
                    continue;   // şablondaki örnek satır
                }
                if (! $type || ! $name || ! $brand || ! $w || ! $d || ! $h) {
                    $res['skipped']++;
                    $res['errors'][] = 'Ürünler satır '.($i + 1).': tür, ad, marka ve üç ölçü zorunlu.';
                    continue;
                }
                $sku = trim((string) $r[3]) ?: null;
                PlannerProduct::query()->updateOrCreate(
                    $sku ? ['brand' => $brand, 'sku' => $sku] : ['brand' => $brand, 'name' => $name],
                    [
                        'type' => $type, 'name' => mb_substr($name, 0, 150), 'brand' => mb_substr($brand, 0, 120), 'sku' => $sku,
                        'width' => $w, 'depth' => $d, 'height' => $h,
                        'width_min' => $mm($r[7]), 'width_max' => $mm($r[8]), 'depth_min' => $mm($r[9]), 'depth_max' => $mm($r[10]),
                        'color' => $hex($r[11]) ?? '#FFFFFF', 'cabinet_color' => $hex($r[12]),
                        'tank' => mb_strtolower(trim((string) $r[13])) === 'evet',
                        'price_min' => $num($r[14]), 'price_max' => $num($r[15]), 'product_url' => $url($r[16]),
                    ]
                );
                $res['products']++;
            }
        }

        if ($s = $book->getSheetByName('Seramikler')) {
            $usage = ['Zemin' => 'floor', 'Duvar' => 'wall', 'İkisi' => 'both'];
            foreach ($s->toArray(null, true, false, false) as $i => $r) {
                if ($i === 0 || ! array_filter($r, fn ($v) => $v !== null && $v !== '')) {
                    continue;
                }
                $name = trim((string) $r[0]);
                $brand = trim((string) $r[1]);
                if ($brand === 'Örnek Marka') {
                    continue;   // şablondaki örnek satır
                }
                [$tw, $th] = [$mm($r[4]), $mm($r[5])];
                if (! $name || ! $brand || ! $tw || ! $th) {
                    $res['skipped']++;
                    $res['errors'][] = 'Seramikler satır '.($i + 1).': ad, marka, en ve boy zorunlu.';
                    continue;
                }
                $sku = trim((string) $r[3]) ?: null;
                PlannerTile::query()->updateOrCreate(
                    $sku ? ['brand' => $brand, 'sku' => $sku] : ['brand' => $brand, 'name' => $name],
                    [
                        'name' => mb_substr($name, 0, 150), 'brand' => mb_substr($brand, 0, 120), 'series' => trim((string) $r[2]) ?: null, 'sku' => $sku,
                        'width_cm' => $tw, 'height_cm' => $th, 'finish' => trim((string) $r[6]) ?: null, 'usage' => $usage[trim((string) $r[7])] ?? 'both',
                        'price_m2_min' => $num($r[8]), 'price_m2_max' => $num($r[9]), 'product_url' => $url($r[10]),
                    ]
                );
                $res['tiles']++;
            }
        }

        return $res;
    }
}
