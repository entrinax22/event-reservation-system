<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservedMaterial extends Model
{
    protected $table = 'reserved_materials';
    protected $primaryKey = 'reserved_material_id';
    protected $fillable = [
        'reserved_event_id',
        'material_id',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'material_id');
    }
}
