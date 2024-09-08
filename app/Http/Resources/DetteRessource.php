<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetteRessource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray( $request): array
    {
        // $this->load('articles');

       return [

            'id' => $this->id,
            'montant' => $this->montant,
            'montantDu' => $this->montant_du,
            'montantRestant' => $this->montant_restant,
            'client_id' => $this->client_id,
            'articles' => new ArticleCollection($this->articles),
       ];
    }
}
