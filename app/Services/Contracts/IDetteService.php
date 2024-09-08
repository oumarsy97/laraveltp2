<?php
namespace App\Services\Contracts;
interface IDetteService
{
    public function all();
    public function store(array $data);
    public function update(array $data,int $id);
    public function delete(int $id);
    public function show(int $id);
    public function query();
    public function listArticle(int $id);
    public function listPaiement(int $id);
}
