<?php

namespace App\Services\Contracts;

interface IArticleService
{
    public function getAll();
    public function create(array $data);
    public function find(int $id);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function findByLibelle(string $libelle);
    public function findByEtat(string $etat);
}
