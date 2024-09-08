<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dette extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['date', 'montant', 'client_id']; // Retirer 'montantDu' et 'montantRestant'
    protected $dates = ['date'];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    // Relations
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

    // Accesseur pour calculer le montant dû
    public function getMontantDuAttribute()
    {
        return $this->paiements()->sum('montant');
    }

    // Accesseur pour calculer le montant restant
    public function getMontantRestantAttribute()
    {
        return $this->montant - $this->montant_du;
    }
}

