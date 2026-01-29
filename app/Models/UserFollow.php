<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserFollow extends Pivot
{
    protected $table = 'user_follows';

    public $timestamps = false;

    protected $fillable = [
        'follower_id',
        'followed_id',
    ];
}
