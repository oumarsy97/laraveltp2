<?php
namespace App\Repositories\Contracts;

interface IClientRepository
{
    public function getAll();
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function findByLibelle(string $libelle);
    public function findByEtat(string $etat);
    public function findByTelephone(string $telephone);
    public function findWithUser(int $id);
    public function query();
    public function filterByUser($query, $user);
    public function filterByEtat($query, $etat);
}
