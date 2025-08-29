<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_request_id',
        'item_id',
        'start_date',
        'end_date',
        'quantity',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function request()
    {
        return $this->belongsTo(ItemRequest::class, 'item_request_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}