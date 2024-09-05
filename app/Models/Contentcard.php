<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contentcard extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'slug', 'blvckbox_id', 'background'];

    public function blvckbox()
    {
        return $this->belongsTo(Blvckbox::class);
    }
    
    public function blvckcards()
    {
        return $this->hasMany(Blvckcard::class);
    }
}
