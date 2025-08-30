<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'event_id';
    protected $fillable = [
        'event_name',
        'event_description'
    ];

    public function reservedEvents()
    {
        return $this->hasMany(ReservedEvent::class, 'event_id', 'event_id');
    }
}
