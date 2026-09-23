<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
    /**
     * Admin mi? (AdminMiddleware tarafından kullanılır)
     */
    public function isAdmin(): bool
    {
        // 1) Filament panel erişimi varsa
        // 2) veya email admin@kainta.com ise
        // 3) veya is_admin alanı true ise
        return strtolower((string) $this->email) === 'admin@kainta.com'
            || ($this->is_admin ?? false) === true;
    }
    public function canAccessPanel(Panel $panel): bool
    {
        return strtolower((string) $this->email) === 'admin@kainta.com';
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
