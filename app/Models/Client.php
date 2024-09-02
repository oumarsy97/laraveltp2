<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;


class Client extends Model
{
    use SoftDeletes;
    use HasFactory;
    use Notifiable;
    protected $fillable = [
        'telephone',
        'adresse',
        'surnom'
    ];


    public function dettes()
    {
        return $this->hasMany(Dette::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }



    // public function paiements()
    // {
    //     return $this->hasMany(Paiement::class);
    // }

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'user_id'
    ];
}
