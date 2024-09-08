<?php
namespace App\Services\Contracts;

interface IClientService
{
    public function getAllClients();
    public function createClient(array $data);
    public function getClientById(int $id);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function find(int $id);
    public function filteredClients(?string $compte, ?string $active);
    public function findByTelephone(String $telephone);

}
