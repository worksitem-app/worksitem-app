<?php

use Spatie\Backup\Notifications\Notifiable;
use Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification;
use Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification;
use Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification;
use Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification;
use Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification;
use Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification;
use Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy;
use Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays;
use Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes;

/*
| Worksitem tam yedek (spatie/laravel-backup v10)
|
| Her gece 03:00 (İstanbul): veritabanı + uygulama dosyaları tek zip olarak
| storage/app/spatie-backups/<ad>/ altına yazılır (web'den erişilemez).
| Son 7 günün yedeği tutulur. Zamanlama: routes/console.php
|
|   php artisan backup:run          # elle tam yedek
|   php artisan backup:run --only-db
|   php artisan backup:list         # yedekler ve toplam boyut
|   php artisan backup:clean        # 7 günden eskileri sil
|
| Eski hafif döküm (app:backup-db → storage/app/backups/db-*.sql.gz) de
| 03:45'te çalışmaya devam eder; launch-check ve preflight onu da kontrol eder.
*/

$name = 'worksitem-'.env('APP_ENV', 'production');

return [

    'backup' => [
        'name' => $name,

        'source' => [
            'files' => [
                // Kod git'te de var; asıl değerli olan .env ve storage/app (yüklenen dosyalar)
                'include' => [
                    base_path(),
                ],

                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                    base_path('.git'),
                    base_path('public/build'),
                    storage_path('framework'),
                    storage_path('logs'),
                    storage_path('app/backups'),        // app:backup-db dökümleri (zaten yedek)
                    storage_path('app/spatie-backups'), // bu paketin kendi yedekleri
                    storage_path('app/backup-temp'),
                ],

                'follow_links' => false,
                'ignore_unreadable_directories' => true,
                'relative_path' => base_path(),
            ],

            'databases' => [
                env('DB_CONNECTION', 'mysql'),
            ],
        ],

        'database_dump_compressor' => null,
        'database_dump_file_timestamp_format' => null,
        'database_dump_filename_base' => 'database',
        'database_dump_file_extension' => '',

        'destination' => [
            'compression_method' => ZipArchive::CM_DEFAULT,
            'compression_level' => 1,   // paylaşımlı hostingte düşük CPU (zip biraz büyür)
            'filename_prefix' => '',
            'disks' => [
                'backups',
            ],
            'continue_on_failure' => false,
        ],

        'temporary_directory' => storage_path('app/backup-temp'),

        // Zip .env içerir. Bilgisayara indirilen yedekler için parola önerilir:
        // .env → BACKUP_ARCHIVE_PASSWORD=... (parolayı ayrıca güvenli bir yerde saklayın)
        'password' => env('BACKUP_ARCHIVE_PASSWORD'),
        'encryption' => 'default',

        'verify_backup' => true,
        'tries' => 2,
        'retry_delay' => 60,
    ],

    // Yalnız sorun olunca e-posta; başarılı yedek her gün e-posta atmaz.
    'notifications' => [
        'notifications' => [
            BackupHasFailedNotification::class => ['mail'],
            UnhealthyBackupWasFoundNotification::class => ['mail'],
            CleanupHasFailedNotification::class => ['mail'],
            BackupWasSuccessfulNotification::class => [],
            HealthyBackupWasFoundNotification::class => [],
            CleanupWasSuccessfulNotification::class => [],
        ],

        'notifiable' => Notifiable::class,

        'mail' => [
            'to' => env('BACKUP_NOTIFY_EMAIL', env('COMPANY_EMAIL', 'info@worksitem.com')),
            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'info@worksitem.com'),
                'name' => env('MAIL_FROM_NAME', 'Worksitem'),
            ],
        ],

        'slack' => [
            'webhook_url' => '',
            'channel' => null,
            'username' => null,
            'icon' => null,
        ],

        'discord' => [
            'webhook_url' => '',
            'username' => '',
            'avatar_url' => '',
        ],

        'webhook' => [
            'url' => '',
        ],
    ],

    'log_channel' => null,

    // backup:monitor her sabah: son yedek 1 günden eski ya da toplam 3 GB'ı aşarsa e-posta
    'monitor_backups' => [
        [
            'name' => $name,
            'disks' => ['backups'],
            'health_checks' => [
                MaximumAgeInDays::class => 1,
                MaximumStorageInMegabytes::class => 3000,
            ],
        ],
    ],

    'cleanup' => [
        'strategy' => DefaultStrategy::class,

        // Son 7 gün: hepsi tutulur; daha eskisi silinir (günlük/haftalık/aylık arşiv yok)
        'default_strategy' => [
            'keep_all_backups_for_days' => 7,
            'keep_daily_backups_for_days' => 0,
            'keep_weekly_backups_for_weeks' => 0,
            'keep_monthly_backups_for_months' => 0,
            'keep_yearly_backups_for_years' => 0,
            // Paylaşımlı hosting kotası için emniyet: toplam 3 GB'ı aşarsa en eski silinir
            'delete_oldest_backups_when_using_more_megabytes_than' => 3000,
        ],

        'tries' => 1,
        'retry_delay' => 0,
    ],

];
