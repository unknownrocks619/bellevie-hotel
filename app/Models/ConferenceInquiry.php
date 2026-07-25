<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ConferenceInquiry extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'company', 'event_date', 'guests_count',
        'message', 'status', 'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'event_date'   => 'date',
            'guests_count' => 'integer',
        ];
    }

    public const STATUSES = [
        'new'       => 'New',
        'contacted' => 'Contacted',
        'closed'    => 'Closed',
    ];

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}
