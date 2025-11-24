<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'payment_id';
    
    protected $table = 'payments';

    protected $fillable = [
        'user_id',
        'reserved_event_id',
        'amount',
        'reference_number',
        'payment_proof',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reservedEvent()
    {
        return $this->belongsTo(ReservedEvent::class, 'reserved_event_id');
    }
}
