<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Booking extends Model
{
    protected $fillable = [
        'booking_reference', 'room_id', 'guest_id', 'guest_first_name', 'guest_last_name',
        'guest_email', 'guest_phone', 'check_in', 'check_out', 'nights', 'adults',
        'children', 'price_per_night', 'subtotal', 'tax_amount', 'discount_amount',
        'total_amount', 'status', 'payment_status', 'payment_method', 'special_requests',
        'internal_notes', 'cancellation_token', 'cancelled_at', 'cancellation_reason', 'source'
    ];

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'cancelled_at' => 'datetime',
            'price_per_night' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->booking_reference) {
                $model->booking_reference = 'BLV-' . strtoupper(Str::random(8));
            }
            if (!$model->cancellation_token) {
                $model->cancellation_token = Str::random(32);
            }
        });
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function getGuestNameAttribute(): string
    {
        return "{$this->guest_first_name} {$this->guest_last_name}";
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'confirmed' => 'badge-info',
            'checked_in' => 'badge-success',
            'checked_out' => 'badge-secondary',
            'cancelled' => 'badge-danger',
            'no_show' => 'badge-dark',
            default => 'badge-light',
        };
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['confirmed', 'checked_in']);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('check_in', today());
    }

    public function scopeCheckingOut(Builder $query): Builder
    {
        return $query->whereDate('check_out', today());
    }
}
