<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Dette extends Model
{
    protected $fillable = ['date', 'montant', 'montantDu', 'montantRestant'];
    protected $dates = ['date'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    use SoftDeletes;
    use HasFactory;
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_dettes')
                    ->withPivot('qteVente', 'prixVente')
                    ->withTimestamps();
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }
}
