<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ArticleDette extends Model
{
    protected $fillable = ['id_article', 'id_dette', 'qteVente', 'prixVente'];

    use HasFactory;
    public function store(Request $request)
{
    $dette = Dette::create($request->only('date', 'montant', 'montantDu', 'montantRestant', 'client_id'));

    foreach ($request->articles as $article) {
        $dette->articles()->attach($article['id'], [
            'qteVente' => $article['qteVente'],
            'prixVente' => $article['prixVente']
        ]);
    }

    return response()->json(['status' => 201, 'data' => $dette, 'message' => 'Dette created successfully'], 201);
}
}
