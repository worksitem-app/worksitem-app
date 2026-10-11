<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class SendDailyCeoReport extends Command
{
    protected $signature = 'reports:daily-ceo {--dry-run}';

    protected $description =
        'WORKSITEM günlük CEO operasyon, lead ve piyasa raporunu gönderir';

    public function handle(): int
    {
        $tz = 'Europe/Istanbul';

        $now = Carbon::now($tz);
        $from = $now->copy()->subDay();

        $fromDb = $from->copy()->utc();
        $toDb = $now->copy()->utc();

        $to = config('worksitem_leads.ceo_report_email')
            ?: config('worksitem_leads.notification_email')
            ?: config('mail.from.address')
            ?: 'info@worksitem.com';

        $leadCount = 0;
        $hotCount = 0;
        $salesReadyCount = 0;
        $pipeline = 0.0;
        $leadSources = [];
        $hotLeads = [];

        if (
            $this->hasTable('leads') &&
            $this->hasColumn('leads', 'created_at')
        ) {
            $q = DB::table('leads')
                ->whereBetween('created_at', [$fromDb, $toDb]);

            $leadCount = (clone $q)->count();

            if ($this->hasColumn('leads', 'lead_quality')) {
                $hotCount = (clone $q)
                    ->where('lead_quality', 'hot')
                    ->count();
            }

            if ($this->hasColumn('leads', 'sales_ready')) {
                $salesReadyCount = (clone $q)
                    ->where('sales_ready', 1)
                    ->count();
            }

            if ($this->hasColumn('leads', 'estimated_cost')) {
                $pipeline = (float) ((clone $q)->sum('estimated_cost') ?? 0);
            }

            if ($this->hasColumn('leads', 'utm_source')) {
                $leadSources = (clone $q)
                    ->whereNotNull('utm_source')
                    ->where('utm_source', '<>', '')
                    ->select(
                        'utm_source',
                        DB::raw('COUNT(*) AS total')
                    )
                    ->groupBy('utm_source')
                    ->orderByDesc('total')
                    ->limit(5)
                    ->get()
                    ->map(fn ($r) => [
                        $r->utm_source,
                        (int) $r->total,
                    ])
                    ->all();
            }

            $select = ['id'];

            foreach ([
                'name',
                'lead_score',
                'lead_quality',
                'sales_ready',
                'estimated_cost',
                'project_type',
                'status',
            ] as $column) {
                if ($this->hasColumn('leads', $column)) {
                    $select[] = $column;
                }
            }

            $hasSalesReady =
                $this->hasColumn('leads', 'sales_ready');

            $hasQuality =
                $this->hasColumn('leads', 'lead_quality');

            $hotLeads = (clone $q)
                ->where(function ($x) use (
                    $hasSalesReady,
                    $hasQuality
                ) {
                    if ($hasSalesReady) {
                        $x->where('sales_ready', 1);
                    }

                    if ($hasQuality) {
                        if ($hasSalesReady) {
                            $x->orWhere(
                                'lead_quality',
                                'hot'
                            );
                        } else {
                            $x->where(
                                'lead_quality',
                                'hot'
                            );
                        }
                    }
                })
                ->orderByDesc(
                    $this->hasColumn(
                        'leads',
                        'lead_score'
                    )
                        ? 'lead_score'
                        : 'id'
                )
                ->limit(5)
                ->get($select)
                ->all();
        }

        $toolCount = 0;
        $toolTop = [];

        if (
            $this->hasTable('tool_events') &&
            $this->hasColumn('tool_events', 'created_at')
        ) {
            $toolCount = DB::table('tool_events')
                ->whereBetween(
                    'created_at',
                    [$fromDb, $toDb]
                )
                ->count();

            $columns =
                Schema::getColumnListing('tool_events');

            $label = null;

            foreach ([
                'tool_slug',
                'tool',
                'event_name',
                'event',
                'name',
            ] as $candidate) {
                if (in_array(
                    $candidate,
                    $columns,
                    true
                )) {
                    $label = $candidate;
                    break;
                }
            }

            if ($label) {
                $toolTop = DB::table('tool_events')
                    ->whereBetween(
                        'created_at',
                        [$fromDb, $toDb]
                    )
                    ->whereNotNull($label)
                    ->where($label, '<>', '')
                    ->select(
                        $label,
                        DB::raw('COUNT(*) AS total')
                    )
                    ->groupBy($label)
                    ->orderByDesc('total')
                    ->limit(8)
                    ->get()
                    ->map(fn ($r) => [
                        $r->{$label},
                        (int) $r->total,
                    ])
                    ->all();
            }
        }

        $projectCount =
            $this->countRecent(
                'projects',
                $fromDb,
                $toDb
            );

        $newUsers =
            $this->countRecent(
                'users',
                $fromDb,
                $toDb
            );

        $notifications =
            $this->countRecent(
                'notifications',
                $fromDb,
                $toDb
            );

        $failedJobs =
            $this->hasTable('failed_jobs')
                ? DB::table('failed_jobs')->count()
                : 0;

        $priceRows = [];

        if (
            $this->hasTable(
                'material_price_observations'
            )
        ) {
            $rows = DB::table(
                'material_price_observations'
            )
                ->select(
                    'material_key',
                    'price',
                    'unit',
                    'source_name',
                    'source_date',
                    'market_level',
                    'verification_status',
                    'observed_at'
                )
                ->orderByDesc('observed_at')
                ->limit(50)
                ->get();

            $seen = [];

            foreach ($rows as $row) {
                if (isset($seen[$row->material_key])) {
                    continue;
                }

                $seen[$row->material_key] = true;
                $priceRows[] = $row;

                if (count($priceRows) >= 8) {
                    break;
                }
            }
        }

        $money = fn ($value) =>
            number_format(
                (float) $value,
                0,
                ',',
                '.'
            );

        $e = fn ($value) =>
            htmlspecialchars(
                (string) $value,
                ENT_QUOTES,
                'UTF-8'
            );

        $html =
            '<!doctype html><html><body ' .
            'style="font-family:Arial,sans-serif;' .
            'background:#f5f6f8;padding:24px;color:#16181d">' .
            '<div style="max-width:760px;margin:auto;' .
            'background:#fff;border:1px solid #e5e7eb;' .
            'border-radius:16px;padding:24px">';

        $html .=
            '<h1 style="margin:0 0 6px">' .
            'WORKSİTEM Günlük CEO Raporu</h1>';

        $html .=
            '<div style="color:#6b7280;margin-bottom:22px">' .
            'Son 24 saat · ' .
            $e($from->format('d.m.Y H:i')) .
            ' – ' .
            $e($now->format('d.m.Y H:i')) .
            ' İstanbul</div>';

        $html .= '<h2>1. İş / Lead</h2>';

        $html .=
            '<p><b>Yeni lead:</b> ' .
            $leadCount .
            ' &nbsp; <b>Sıcak:</b> ' .
            $hotCount .
            ' &nbsp; <b>Sales-ready:</b> ' .
            $salesReadyCount .
            '</p>';

        $html .=
            '<p><b>Tahmini proje hacmi:</b> ' .
            $money($pipeline) .
            ' TL</p>';

        if ($leadSources) {
            $html .=
                '<p><b>Lead kaynakları:</b> ';

            foreach (
                $leadSources
                as [$source, $count]
            ) {
                $html .=
                    $e($source) .
                    ' (' .
                    $count .
                    ') · ';
            }

            $html .= '</p>';
        }

        if ($hotLeads) {
            $html .=
                '<h3>Öncelikli leadler</h3><ul>';

            foreach ($hotLeads as $lead) {
                $name =
                    $lead->name
                    ?? ('Lead #' . $lead->id);

                $score =
                    isset($lead->lead_score)
                        ? ' · skor ' .
                            $lead->lead_score
                        : '';

                $cost =
                    isset($lead->estimated_cost)
                        ? ' · ' .
                            $money(
                                $lead->estimated_cost
                            ) .
                            ' TL'
                        : '';

                $html .=
                    '<li>' .
                    $e($name) .
                    $e($score) .
                    $e($cost) .
                    '</li>';
            }

            $html .= '</ul>';
        }

        $html .= '<h2>2. Site Hareketi</h2>';

        $html .=
            '<p><b>Araç eventleri:</b> ' .
            $toolCount .
            ' &nbsp; <b>Yeni proje:</b> ' .
            $projectCount .
            ' &nbsp; <b>Yeni kullanıcı:</b> ' .
            $newUsers .
            ' &nbsp; <b>Bildirim:</b> ' .
            $notifications .
            '</p>';

        if ($toolTop) {
            $html .=
                '<h3>En çok kullanılan araç/eventler</h3><ul>';

            foreach (
                $toolTop
                as [$label, $count]
            ) {
                $html .=
                    '<li>' .
                    $e($label) .
                    ' — ' .
                    $count .
                    '</li>';
            }

            $html .= '</ul>';
        }

        $html .= '<h2>3. Piyasa</h2>';

        if ($priceRows) {
            $html .=
                '<table cellpadding="7" cellspacing="0" ' .
                'border="0" style="width:100%;' .
                'border-collapse:collapse">';

            $html .=
                '<tr style="background:#f3f4f6">' .
                '<th align="left">Kalem</th>' .
                '<th align="right">Fiyat</th>' .
                '<th align="left">Kaynak</th>' .
                '<th align="left">Durum</th>' .
                '</tr>';

            foreach ($priceRows as $row) {
                $status =
                    $row->verification_status
                    ?? $row->market_level
                    ?? '—';

                $html .=
                    '<tr><td>' .
                    $e($row->material_key) .
                    '</td><td align="right">' .
                    $money($row->price) .
                    ' ' .
                    $e($row->unit) .
                    '</td><td>' .
                    $e($row->source_name) .
                    '</td><td>' .
                    $e($status) .
                    '</td></tr>';
            }

            $html .= '</table>';
        } else {
            $html .=
                '<p>Piyasa gözlemi bulunamadı.</p>';
        }

        $html .= '<h2>4. Sistem</h2>';

        $html .=
            '<p><b>Failed jobs:</b> ' .
            $failedJobs .
            '</p>';

        $html .=
            '<p style="color:#6b7280;font-size:12px">' .
            'Not: Bu rapor Worksitem içindeki kayıtlı ' .
            'operasyon hareketlerini özetler. ' .
            'Google Analytics/Search Console trafiği ' .
            'henüz dahil değildir.' .
            '</p>';

        $html .=
            '<hr><div style="color:#6b7280;font-size:12px">' .
            'WORKSİTEM · Kainta satış operasyonu' .
            '</div></div></body></html>';

        if ($this->option('dry-run')) {
            $this->line('DRY_RUN=YES');
            $this->line('LEADS=' . $leadCount);
            $this->line('HOT=' . $hotCount);
            $this->line(
                'SALES_READY=' .
                $salesReadyCount
            );
            $this->line(
                'PIPELINE=' .
                $pipeline
            );
            $this->line(
                'TOOL_EVENTS=' .
                $toolCount
            );
            $this->line(
                'PROJECTS=' .
                $projectCount
            );
            $this->line(
                'USERS=' .
                $newUsers
            );
            $this->line(
                'FAILED_JOBS=' .
                $failedJobs
            );
            $this->line(
                'PRICE_ROWS=' .
                count($priceRows)
            );

            return self::SUCCESS;
        }

        Mail::html(
            $html,
            function ($message) use ($to) {
                $message
                    ->to($to)
                    ->subject(
                        'WORKSİTEM Günlük CEO Raporu'
                    );
            }
        );

        $this->info('REPORT_SENT=YES');
        $this->info('RECIPIENT=' . $to);
        $this->info(
            'LEADS=' .
            $leadCount .
            ' HOT=' .
            $hotCount .
            ' SALES_READY=' .
            $salesReadyCount
        );
        $this->info(
            'PIPELINE=' .
            $pipeline .
            ' TL'
        );
        $this->info(
            'TOOL_EVENTS=' .
            $toolCount
        );
        $this->info(
            'PRICE_ROWS=' .
            count($priceRows)
        );

        return self::SUCCESS;
    }

    private function hasTable(
        string $table
    ): bool {
        return Schema::hasTable($table);
    }

    private function hasColumn(
        string $table,
        string $column
    ): bool {
        return
            $this->hasTable($table) &&
            Schema::hasColumn(
                $table,
                $column
            );
    }

    private function countRecent(
        string $table,
        Carbon $from,
        Carbon $to
    ): int {
        if (
            !$this->hasColumn(
                $table,
                'created_at'
            )
        ) {
            return 0;
        }

        return DB::table($table)
            ->whereBetween(
                'created_at',
                [$from, $to]
            )
            ->count();
    }
}
