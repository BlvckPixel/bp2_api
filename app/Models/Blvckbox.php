<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blvckbox extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'slug', 'subtitle', 'description', 'date', 'background'];

    public function blvckcards()
    {
        return $this->hasMany(Blvckcard::class);
    }

    public function contentCards()
    {
        return $this->hasMany(ContentCard::class);
    }

    public function editorials()
    {
        return $this->hasMany(Editorial::class);
    }
}
