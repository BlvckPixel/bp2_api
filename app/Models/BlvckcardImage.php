<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlvckcardImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'blvckcard_id', 'image_path',
    ];

    public function blvckcard()
    {
        return $this->belongsTo(Blvckcard::class);
    }
}
