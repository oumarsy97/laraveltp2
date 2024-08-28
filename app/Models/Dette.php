<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dette extends Model
{
    protected $fillable = ['date', 'montant', 'montantDu', 'montantRestant', 'client_id'];

    use HasFactory;
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_dette')
                    ->withPivot('qteVente', 'prixVente')
                    ->withTimestamps();
    }
}
