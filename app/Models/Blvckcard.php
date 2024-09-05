<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blvckcard extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'description', 'teaser_description', 'date', 'blvckbox_id', 'contentcard_id', 'meta_keywords', 'user_id'];

    public function blvckbox()
    {
        return $this->belongsTo(Blvckbox::class);
    }

    public function images()
    {
        return $this->hasMany(BlvckcardImage::class);
    }

    public function contentCard()
    {
        return $this->belongsTo(ContentCard::class);
    }
}
