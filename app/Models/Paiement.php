<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;
    protected $fillable = ['dette_id', 'montant', ];
    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];


    public function dette()
    {
        return $this->belongsTo(Dette::class);
    }
}
