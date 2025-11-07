<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        // todo: create another panel for customer users, then we can allow the customer users to access their own panel
        if ($panel->getId() === 'admin') {
            // ray($this->hasRole('customer')); //false
            return $this->email === config('app.dashboard.allowed-admin-email') && $this->hasVerifiedEmail();

        }

        // ray($this->hasRole('customer')); //true...

        return true;
    }

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

    protected $appends = ['has_billing_info', 'has_shipping_info'];

    public function userInfoEntries(): HasMany
    {
        return $this->hasMany(UserInfoEntry::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function billingInfoEntry(): HasMany
    {
        return $this->hasMany(UserInfoEntry::class)->where('type', 'billing');
    }

    public function shippingInfoEntry(): HasMany
    {
        return $this->hasMany(UserInfoEntry::class)->where('type', 'shipping');
    }

    public function mainBillingInfoEntry(): ?UserInfoEntry
    {
        return $this->billingInfoEntry->where('is_main', true)->first();
    }

    public function mainShippingInfoEntry(): ?UserInfoEntry
    {
        return $this->shippingInfoEntry->where('is_main', true)->first();
    }

    public function hasBillingInfo(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->billingInfoEntry->count() > 0
        );
    }

    public function hasShippingInfo(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->shippingInfoEntry->count() > 0
        );
    }
}
