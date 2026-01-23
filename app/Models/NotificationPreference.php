<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationPreference extends Model
{
  protected $fillable = ['user_id', 'type', 'enabled'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
}
