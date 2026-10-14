<?php

namespace App\Console\Commands;

use App\Models\MaterialPriceHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyMaterialPriceReport extends Command
{
    protected $signature = 'prices:daily-material-report';

    protected $description =
        'Son 24 saatte eklenen malzeme fiyatlarını e-posta ile gönderir';

    public function handle(): int
    {
        $recipient = trim(
            (string) config(
                'worksitem_prices.report_email'
            )
        );

        if ($recipient === '') {
            $this->error('PRICE_REPORT_EMAIL tanımlı değil.');

            return self::FAILURE;
        }

        $from = now()->subHours(24);
        $to = now();

        $rows = MaterialPriceHistory::query()
            ->with('material')
            ->where('created_at', '>=', $from)
            ->where('created_at', '<=', $to)
            ->orderByDesc('created_at')
            ->get();

        $approvedCount = $rows
            ->where('verification_status', 'approved')
            ->count();

        $reviewCount = $rows
            ->where('verification_status', 'needs_review')
            ->count();

        $subject = sprintf(
            'WORKSITEM | Günlük Malzeme Fiyatları | %s | %d yeni kayıt',
            $to->format('d.m.Y'),
            $rows->count()
        );
        if (\Illuminate\Support\Facades\Schema::hasTable('price_robot_items')) {
            $pending = \App\Models\PriceRobotItem::query()->whereNotNull('proposed_value')->count();
            $subject .= $pending ? ' | '.$pending.' fiyat onay bekliyor' : '';
        }

        Mail::send(
            'emails.daily-material-prices',
            [
                'rows' => $rows,
                'from' => $from,
                'to' => $to,
                'approvedCount' => $approvedCount,
                'reviewCount' => $reviewCount,
                'robot' => \Illuminate\Support\Facades\Schema::hasTable('price_robot_items')
                    ? \App\Models\PriceRobotItem::query()->where('researched_at', '>=', $from)->orderByDesc('proposed_value')->orderBy('label')->get()
                    : collect(),
            ],
            function ($message) use (
                $recipient,
                $subject
            ): void {
                $message
                    ->to($recipient)
                    ->subject($subject);
            }
        );

        $this->info(
            'REPORT_SENT=YES'
        );

        $this->line(
            'RECIPIENT='.$recipient
        );

        $this->line(
            'FROM='.$from->format('Y-m-d H:i:s')
        );

        $this->line(
            'TO='.$to->format('Y-m-d H:i:s')
        );

        $this->line(
            'NEW_PRICE_COUNT='.$rows->count()
        );

        $this->line(
            'APPROVED_COUNT='.$approvedCount
        );

        $this->line(
            'NEEDS_REVIEW_COUNT='.$reviewCount
        );

        foreach ($rows as $row) {
            $this->line(
                $row->material_key.
                ' | '.
                $row->price.
                ' '.
                $row->unit.
                ' | '.
                $row->verification_status
            );
        }

        return self::SUCCESS;
    }
}
