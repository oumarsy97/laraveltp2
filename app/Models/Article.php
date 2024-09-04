<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'libelle',
        'qteStock',
        'prix'
    ];
    public function dettes()
    {
        return $this->belongsToMany(Dette::class, 'article_dettes', )
                    ->withPivot('qteVente', 'prixVente')
                    ->withTimestamps();
    }

     /**
     * Scope a query to filter articles by libelle.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $libelle
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByLibelle($query, $libelle)
    {
        return $query->where('libelle', 'like', "%{$libelle}%");
    }

    protected $hidden = [ 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'qteStock' => 'integer',
        'prix' => 'float',
    ];
    protected $dates = ['deleted_at'];
}
