<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'material_id';
    protected $fillable = [
        'material_name',
        'material_description',
        'material_quantity',
        'status'
    ];

    public function reservedEvents()
    {
        return $this->belongsToMany(ReservedEvent::class, 'reserved_materials', 'material_id', 'reserved_event_id');
    }
}
