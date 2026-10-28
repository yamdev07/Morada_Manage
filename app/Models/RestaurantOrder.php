<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantOrder extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'guest_name',
        'room_id',
        'transaction_id',
        'order_type',
        'table_number',
        'total',
        'status',
        'notes',
        'payment_method',
        'paid_at',
        'served_at',
        'created_by',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'served_at' => 'datetime',
    ];

    /** Cycle de vie d'une commande */
    public const STATUSES = [
        'pending' => 'En attente',
        'preparing' => 'En préparation',
        'delivered' => 'Servie',
        'paid' => 'Payée',
        'cancelled' => 'Annulée',
    ];

    public const TYPES = [
        'dining' => 'Sur place',
        'room_service' => 'Room service',
        'takeaway' => 'À emporter',
    ];

    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::nextOrderNumber();
            }
        });
    }

    public static function nextOrderNumber()
    {
        $lastId = (int) static::max('id');

        do {
            $lastId++;
            $number = 'CMD-'.str_pad($lastId, 5, '0', STR_PAD_LEFT);
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    // ==================== RELATIONS ====================

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(RestaurantOrderItem::class, 'order_id');
    }

    // ==================== SCOPES ====================

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['pending', 'preparing']);
    }

    public function scopeStatus($query, $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }

    // ==================== ACCESSEURS ====================

    public function getStatusLabelAttribute()
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'warning',
            'preparing' => 'info',
            'delivered' => 'primary',
            'paid' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public function getTypeLabelAttribute()
    {
        return self::TYPES[$this->order_type] ?? 'Sur place';
    }

    public function getFormattedTotalAttribute()
    {
        return \App\Helpers\Helper::formatCFA($this->total);
    }

    /** Où servir : chambre, table ou à emporter */
    public function getDestinationAttribute()
    {
        if ($this->room_id && $this->room) {
            return 'Chambre '.$this->room->number;
        }

        if ($this->table_number) {
            return 'Table '.$this->table_number;
        }

        return $this->type_label;
    }

    /** Nom a afficher : fiche client, nom libre, ou client de passage */
    public function getClientNameAttribute()
    {
        return $this->customer->name ?? $this->guest_name ?? 'Client de passage';
    }

    public function getIsEditableAttribute()
    {
        return ! in_array($this->status, ['paid', 'cancelled']);
    }

    /** Recalcule le total à partir des lignes de la commande */
    public function recalculateTotal()
    {
        $this->total = $this->items->sum(fn ($item) => $item->price * $item->quantity);
        $this->save();

        return $this->total;
    }
}
