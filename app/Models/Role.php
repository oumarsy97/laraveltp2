<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Role extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'libelle',

    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

   protected $casts = [
       'id' => 'integer',
       'libelle' => 'string',
   ];

   protected $hidden = [
       'created_at',
       'updated_at',
       'deleted_at',
   ];
}
