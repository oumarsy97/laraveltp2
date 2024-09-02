<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray($request)
    {
        
        return $this->collection->map(function ($article) {
            return [
                'qteVente' => $article->pivot->qteVente,
                'prixVente' => $article->pivot->prixVente,
            ];
        })->all();
    }
}
