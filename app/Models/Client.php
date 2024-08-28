<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasFactory;
    use Notifiable;
    protected $fillable = [
        'telephone',
        'adresse',
        'user_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dettes()
    {
        return $this->hasMany(Dette::class);
    }

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
