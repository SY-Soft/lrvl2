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
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isGod() || $this->hasAnyRole(['admin', 'manager']);
    }

    public function isAdmin(): bool
    {
        return $this->isGod() || $this->hasAnyRole(['admin', 'manager']);
    }

    public function isGod(): bool
    {
        return in_array($this->email, config('services.filament_admin_emails', []), true);
    }

    public function isSupport(): bool
    {
        return $this->hasRole('support');
    }

    public function canManageTickets(): bool
    {
        return $this->isAdmin();
    }

    public function canWorkTickets(): bool
    {
        return $this->isAdmin() || $this->isSupport();
    }
}
