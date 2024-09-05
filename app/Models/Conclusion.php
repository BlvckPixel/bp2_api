<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conclusion extends Model
{
    use HasFactory;

    protected $fillable = ['blvckbox_id', 'section', 'background_image'];

    public function blvckbox()
    {
        return $this->belongsTo(Blvckbox::class);
    }
}
