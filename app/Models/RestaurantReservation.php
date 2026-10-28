<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantReservation extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'reservation_date', 'reservation_time',
        'persons', 'table_type', 'notes', 'status', 'source', 'handled_by',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'persons' => 'integer',
    ];

    public const STATUSES = [
        'pending' => 'À confirmer',
        'confirmed' => 'Confirmée',
        'seated' => 'Installée',
        'completed' => 'Terminée',
        'cancelled' => 'Annulée',
        'no_show' => 'Non honorée',
    ];

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('reservation_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->whereDate('reservation_date', '>=', today())
            ->whereIn('status', ['pending', 'confirmed']);
    }

    public function getStatusLabelAttribute()
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'warning',
            'confirmed' => 'success',
            'seated' => 'info',
            'completed' => 'secondary',
            'cancelled', 'no_show' => 'danger',
            default => 'secondary',
        };
    }

    /** Date + heure fusionnées, pratique pour trier et afficher */
    public function getScheduledAtAttribute()
    {
        return \Carbon\Carbon::parse(
            $this->reservation_date->format('Y-m-d').' '.$this->reservation_time
        );
    }

    public function getFormattedTimeAttribute()
    {
        return \Carbon\Carbon::parse($this->reservation_time)->format('H:i');
    }
}
