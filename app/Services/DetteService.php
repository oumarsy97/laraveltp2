<?php

namespace App\Services;

use App\Http\Resources\DetteRessource;
use App\Models\Article;
use App\Models\Dette;
use App\Repositories\Contracts\IDetteRepository;
use App\Services\Contracts\IDetteService;
use Illuminate\Support\Facades\DB;

class DetteService implements IDetteService
{
    protected $dettesRepository;
    public function __construct(IDetteRepository $dettesRepository)
    {
        $this->dettesRepository = $dettesRepository;
    }

    public function store(array $data){
        $errors = [];
        $articles = [];
        $montant = 0;


        foreach ($data['articles'] as $article) {
            $articleModel = Article::find($article['articleId']);
            if(!$articleModel) {
                $errors[] = "Article ID: " . $article['articleId'] . " introuvable.";
            }else{
                if ($articleModel->qteStock < $article['qteVente']) {
                    $errors[] = "La quantité de l'article ID: " . $article['articleId'] . " est insuffisante.";
                    continue;
                }
                $articles[] = $article;
                $montant += $article['prixVente'] * $article['qteVente'];
            }

        }



        $dette = DB::transaction(function () use ($articles, $data,$montant, $errors) {
            // 1. Mettre à jour le stock
            foreach ($articles as $article) {
                $articleModel = Article::find($article['articleId']);
                $articleModel->qteStock -= $article['qteVente'];
                $articleModel->save();
            }

            // 2. Créer la dette
            $dette = new Dette();
            $dette->client_id = $data['clientId'];
            $dette->montant = $montant;
            $dette->save();

            // 3.  attacher les articles à la dette
            foreach ($articles as $article) {
                $dette->articles()->attach($article['articleId'], [
                    'qteVente' => $article['qteVente'],
                    'prixVente' => $article['prixVente'],
                ]);
            }
            // 4. Enregistrer le paiement
            if($data['paiement']['montant'] > $montant) {
                $errors[] = "Le paiement doit être inferieur au montant de la dette.";
            }elseif($data['paiement']['montant'] < 0){
                $errors[] = "Le paiement doit etre superieur a 0.";
            }
            else{
                $dette->paiements()->create([
                    'montant' => $data['paiement']['montant'],
                ]);
            }


        //     // Retourner la dette créée avec les relations
             return [$dette->load('articles'), $errors];
        });


        $data = [
            'dette' => new DetteRessource($dette[0]),
            'errors' => $dette[1],
        ];
        return $data;

    }

    public function all($solde = null)
    {
        $dettes = $this->dettesRepository->getDettes($solde);
        if($solde === 'oui') {
            //filter par montantRestant
            $dettes = $dettes->filter(function ($dette) {
                return $dette->montantRestant == 0;
            });
        } elseif ($solde === 'non') {
            //filter par montantDu
            $dettes = $dettes->filter(function ($dette) {
                return $dette->montantRestant > 0;
            });
        }
        return $dettes;

    }


    public function update(array $data, int $id){
        return $this->dettesRepository->update($data, $id);
    }

    public function delete(int $id){
        return $this->dettesRepository->delete($id);
    }

    public function show(int $id){
        return $this->dettesRepository->show($id);
    }

    public function query(){
        return $this->dettesRepository->query();
    }

    public function listArticle(int $id){
        return $this->dettesRepository->listArticle($id);
    }

    public function listPaiement(int $id){
        return $this->dettesRepository->listPaiement($id);
    }
}
