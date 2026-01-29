<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TagFollow extends Pivot
{
    protected $table = 'tag_follows';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'tag_id',
    ];
}
