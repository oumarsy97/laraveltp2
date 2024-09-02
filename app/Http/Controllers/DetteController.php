<?php

namespace App\Http\Controllers;

use App\Enums\ResponseStatus;
use App\Enums\RoleEnum;
use App\Http\Requests\PaiementRequest;
use App\Http\Requests\StoreDetteRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\DetteRessource;
use App\Models\Article;
use App\Models\Dette;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;


class DetteController extends Controller
{

   public function index(Request $request)
    {
        $solde = $request->query('solde');
        if($solde == 'oui'){
            $dette = Dette::where('montantRestant', '>', 0)->get();
        }
        elseif($solde == 'non'){
            $dette = Dette::where('montantRestant', '=', 0)->get();
        }else {
            $dette = Dette::all();
        }


        return $this->sendResponse($dette, 'Liste des dettes', Response::HTTP_OK,ResponseStatus::SUCCESS);
    }

    public function store(StoreDetteRequest $request)
{
    try{
        DB::transaction(function () use ($request) {
            foreach ($request->articles as $article) {
                $articleModel = Article::find($article['articleId']);
                if ($articleModel->qteStock < $article['qteVente']) {
                    throw new \Exception('Quantité insuffisante pour l\'article ID: ' . $article['articleId']);
                }
            }

            $dette = new Dette();
            $dette->client_id = $request->clientId;
            $dette->montant = $request->montant;
            $dette->montantDu = $request->paiement['montant'];
            $dette->montantRestant = $request->montant - $request->paiement['montant'];
            $dette->save();

            foreach ($request->articles as $article) {
                $articleModel = Article::find($article['articleId']);
                $articleModel->qteStock -= $article['qteVente'];
                $articleModel->save();

                $dette->articles()->attach($article['articleId'], [
                    'qteVente' => $article['qteVente'],
                    'prixVente' => $article['prixVente']
                ]);
            }

            // Enregistrer le paiement
            if($request->paiement['montant'] > 0) {
            $paiement = new Paiement();
            $paiement->dette_id = $dette->id;
            $paiement->montant = $request->paiement['montant'];
            $paiement->save();
            }
        });    //recuperer le dernier dette

        $dette = Dette::latest()->first()->load('articles', 'client');


    return $this->sendResponse($dette, 'Dette enregistrée avec succes', Response::HTTP_OK, ResponseStatus::SUCCESS);
}
catch (\Exception $e) {
    return response()->json(['message' => $e->getMessage()], 500);
}

}

public function show($id)
{
    try{
    $dette = Dette::find($id);
    if (!$dette) {
        return $this->sendResponse(null, 'Dette introuvable', Response::HTTP_NOT_FOUND,ResponseStatus::ECHEC);
    }
    return $this->sendResponse($dette->load('articles', 'client'), 'Dette trouve', Response::HTTP_OK,ResponseStatus::SUCCESS);
}
catch (\Exception $e) {
    return $this->sendResponse(null, $e->getMessage(), Response::HTTP_NOT_FOUND,ResponseStatus::ECHEC);
}

}

public function destroy($id)
{
    try{
    $dette = Dette::find($id);
    if (!$dette) {
        return $this->sendResponse(null, 'Dette introuvable', Response::HTTP_NOT_FOUND,ResponseStatus::ECHEC);
    }
    $dette->delete();
    return $this->sendResponse(null, 'Dette supprimée avec succes', Response::HTTP_OK,ResponseStatus::SUCCESS);
}
catch (\Exception $e) {
    return $this->sendResponse(null, $e->getMessage(), Response::HTTP_NOT_FOUND,ResponseStatus::ECHEC);
}

}

public function listArticleDette(Request $request, $id)
{
    try {
        $data = Dette::with('articles', )->find($id);

        if (!$data) {
            return $this->sendResponse(null, 'Dette introuvable', Response::HTTP_NOT_FOUND, ResponseStatus::ECHEC);
        }

        if (!$request->user()->role == 'BOUTIQUIER' && !$request->user()->role == 'CLIENT') {
            return $this->sendResponse(null, 'Vous n\'avez pas les autorisations requises', Response::HTTP_FORBIDDEN, ResponseStatus::ECHEC);
        }

        if ($request->user()->role->libelle == 'CLIENT') {
            $clientId = $request->user()->client->id ?? null;
            if (!$clientId || $data->client_id !== $clientId) {
                return $this->sendResponse(null, 'Vous n\'avez pas les autorisations requises', Response::HTTP_FORBIDDEN, ResponseStatus::ECHEC);
            }
        }

        $dette = new DetteRessource($data);

        return $this->sendResponse($dette, 'Liste des articles de la dette', Response::HTTP_OK, ResponseStatus::SUCCESS);
    } catch (\Exception $e) {
        return $this->sendResponse(null, $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, ResponseStatus::ECHEC);
    }
}



public function listPaiementDette(Request $request,$id){
    try{
    $dette = Dette::find($id);
    if (!$dette) {
        return $this->sendResponse(null, 'Dette introuvable', Response::HTTP_NOT_FOUND,ResponseStatus::ECHEC);
    }
    if (!$request->user()->role == 'BOUTIQUIER' && !$request->user()->role == 'CLIENT') {
        return $this->sendResponse(null, 'Vous n\'avez pas les autorisations requises', Response::HTTP_FORBIDDEN, ResponseStatus::ECHEC);
    }


    if ($request->user()->role->libelle =='CLIENT') {
        $clientId = $request->user()->client->id ?? null; // Récupère l'ID du client lié à l'utilisateur
        if (!$clientId || $dette->client_id !== $clientId) {
            return $this->sendResponse(null, 'Vous n\'avez pas les autorisations requises', Response::HTTP_FORBIDDEN, ResponseStatus::ECHEC);
        }
    }
    return $this->sendResponse($dette->load('paiements'), 'Liste des paiements', Response::HTTP_OK,ResponseStatus::SUCCESS);
}
catch (\Exception $e) {
    return $this->sendResponse(null, $e->getMessage(), Response::HTTP_NOT_FOUND,ResponseStatus::ECHEC);
}

}

public function paiementDette(PaiementRequest $request, $id){
    try{
    $dette = Dette::find($id);

    if (!$request->user()->role == 'BOUTIQUIER' && !$request->user()->role == 'CLIENT') {
        return $this->sendResponse(null, 'Vous n\'avez pas les autorisations requises', Response::HTTP_FORBIDDEN, ResponseStatus::ECHEC);
    }

    //verifier si le montant restant de la dette = 0
    if ($dette->montantRestant == 0) {
        return $this->sendResponse($dette, 'La dette est deja payée', Response::HTTP_CONFLICT,ResponseStatus::ECHEC);
    }
    if($dette->montantDu >= $request->montant){
    DB::transaction(function () use ($dette, $request) {

        $dette->montantRestant = $dette->montantRestant - $request->montant;
        $dette->montantDu = $dette->montantDu + $request->montant;
        $paiement = new Paiement();
        $paiement->dette_id = $dette->id;
        $paiement->montant = $request->montant;
        $paiement->save();

        $dette->save();
    });
}
if($request->montant > $dette->montantRestant){

    return $this->sendResponse($dette, 'Le montant est superieur au montant restant', Response::HTTP_CONFLICT,ResponseStatus::ECHEC);
}


    return $this->sendResponse($dette->load('paiements'), 'Paiement effectue avec succes', Response::HTTP_OK,ResponseStatus::SUCCESS);
    }
    catch (\Exception $e) {
        return $this->sendResponse(null, $e->getMessage(), Response::HTTP_NOT_FOUND,ResponseStatus::ECHEC);
    }
}


}
