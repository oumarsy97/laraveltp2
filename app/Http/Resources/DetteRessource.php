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
    public function toArray(Request $request): array
    {
       return [
           'id' => $this->id,
           'montant' => $this->montant,
           'montantPaye' => $this->montantDu,
           'montantRestant' => $this->montantRestant,
            'articles' => new ArticleCollection($this->articles),
       ];
    }
}
