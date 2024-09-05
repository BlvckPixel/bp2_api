<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccessLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'accessed_at', 'ip_address', 'user_agent'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
