<?php

namespace App\Repositories;

use App\Models\Dette;
use App\Repositories\Contracts\IDetteRepository;

class DetteRepository implements IDetteRepository
{

    protected $model;
    public function __construct()
    {
        $this->model = new Dette();
    }

    public function all(){

        $dettes = $this->model->with('articles','paiements')->get();
        foreach ($dettes as $dette) {
            $montantDu = $dette->montant_du;
            $montantRestant = $dette->montant_restant;
            $dette['montantDu'] = $montantDu;
            $dette['montantRestant'] = $montantRestant;
        }

        return $dettes;
    }

    public function find($id){
        return $this->model->with('articles')->find($id);
    }

    public function store(array $data){
        return $this->model->create($data);
    }

    public function update( array $data,int $id){
        return $this->model->find($id)->update($data);
    }

    public function show(int $id){
        return $this->model->find($id);
    }

    public function delete(int $id){
        return $this->model->find($id)->delete();
    }

    public function destroy(int $id){
        return $this->model->destroy($id);
    }

    public function query(){
        return $this->model;
    }

    public function getDettes($solde = null)
    {
        // Requête de base pour récupérer les dettes avec les relations nécessaires
        $query = $this->model::query()->with('articles','paiements');

        // Exécuter la requête et récupérer les dettes
        return $query->get()->map(function ($dette) {
            // Ajoute les calculs dynamiques au modèle
            $dette['montantDu']= $dette->montant_du;
            $dette['montantRestant'] = $dette->montant_restant;

            return $dette;
        });
    }

    public function listArticle(int $id) {

        $dette = $this->model::with('articles')->find($id);
        return $dette->articles;
    }

    public function listPaiement(int $id) {
        $dette = $this->model::with('paiements')->find($id);
        return $dette;
    }




}
