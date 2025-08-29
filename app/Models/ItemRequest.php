<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',        // 'inventory' | 'to-buy'
        'item_id',     // nullable se 'to-buy'
        'quantity',
        'start_date',
        'end_date',
        'status',      // pending | approved | rejected
        'note',        // opzionale per 'to-buy'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /* Scopes utili */
    public function scopeInventory($q) { return $q->where('type', 'inventory'); }
    public function scopeToBuy($q)     { return $q->where('type', 'to-buy'); }
}