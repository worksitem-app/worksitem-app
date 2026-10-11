<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Canlı ortamda bilinen şifreli (test@example.com / "password") kullanıcı AÇILMAZ.
     * Admin kullanıcısı için: php artisan make:filament-user  →  ardından is_admin = 1
     */
    public function run(): void
    {
        if (app()->environment('local', 'testing')) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }
    }
}
