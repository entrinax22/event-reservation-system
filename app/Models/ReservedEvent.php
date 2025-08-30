<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservedEvent extends Model
{
    protected $table = 'reserved_events';
    protected $primaryKey = 'reserved_event_id';
    protected $fillable = [
        'user_id',
        'event_id',
        'event_date',
        'event_end_date',
        'total_cost',
        'event_notes',
        'downpayment_amount',
        'status',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    public function materials()
    {
        return $this->belongsToMany(Material::class, 'reserved_materials', 'reserved_event_id', 'material_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
