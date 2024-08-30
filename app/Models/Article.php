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
        return $this->belongsToMany(Dette::class, 'article_dette')
                    ->withPivot('qteVente', 'prixVente')
                    ->withTimestamps();
    }

    protected $hidden = ['pivot', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'qteStock' => 'integer',
        'prix' => 'float',
    ];
    protected $dates = ['deleted_at'];
}
