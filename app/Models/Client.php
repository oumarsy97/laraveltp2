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
        'surnom',
        'user_id'
    ];
    

    public function dettes()
    {
        return $this->hasMany(Dette::class);
    }

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
