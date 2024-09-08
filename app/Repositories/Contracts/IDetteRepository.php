<?php
namespace App\Repositories\Contracts;
interface IDetteRepository
{
    public function show(int $id);
    public function all();
    public function store(array $data);
    public function update(array $data,int $id);
    public function delete(int $id);
    public function query();
    public function getDettes($solde = null);
    public function listArticle(int $id) ;
    public function listPaiement(int $id) ;
}
