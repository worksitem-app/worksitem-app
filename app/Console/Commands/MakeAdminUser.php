<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

/**
 * Filament paneline erişebilen admin oluşturur ya da mevcut kullanıcıyı admin yapar.
 * (make:filament-user is_admin alanını set etmediği için panele giriş yapılamıyordu.)
 *
 *   php artisan app:make-admin ceo@worksitem.com --name="Ad Soyad"
 */
class MakeAdminUser extends Command
{
    protected $signature = 'app:make-admin
        {email : Admin e-posta adresi}
        {--name= : Ad soyad (yeni kullanıcı için)}
        {--password= : Şifre (verilmezse gizli olarak sorulur; en az 12 karakter)}';

    protected $description = 'Admin kullanıcısı oluşturur veya mevcut kullanıcıyı admin yapar';

    public function handle(): int
    {
        $email = strtolower(trim((string) $this->argument('email')));
        $password = $this->option('password') ?? $this->secret('Şifre (en az 12 karakter)');

        $validator = Validator::make(
            ['email' => $email, 'password' => $password],
            ['email' => ['required', 'email'], 'password' => ['required', 'string', 'min:12']],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::firstOrNew(['email' => $email]);
        $isNew = ! $user->exists;

        $user->forceFill([
            'name' => $this->option('name') ?: ($user->name ?: strstr($email, '@', true)),
            'password' => $password,
            'is_admin' => true,
        ])->save();

        $this->info(($isNew ? 'Admin oluşturuldu: ' : 'Kullanıcı admin yapıldı / şifresi güncellendi: ').$email);

        return self::SUCCESS;
    }
}
