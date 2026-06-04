<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guest extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'nationality', 'id_type',
        'id_number', 'date_of_birth', 'address', 'city', 'country', 'zip_code',
        'vip_status', 'special_requests', 'internal_notes', 'is_blacklisted',
        'last_stay_at', 'total_stays', 'total_spent'
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'last_stay_at' => 'datetime',
            'total_spent' => 'decimal:2',
            'is_blacklisted' => 'boolean',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(GuestNote::class);
    }

    protected function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    protected function getVipBadgeClassAttribute(): string
    {
        return match($this->vip_status) {
            'silver' => 'badge-secondary',
            'gold' => 'badge-warning',
            'platinum' => 'badge-dark',
            default => 'badge-light',
        };
    }
}
