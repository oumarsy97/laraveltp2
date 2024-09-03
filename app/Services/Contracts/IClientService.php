<?php
namespace App\Services\Contracts;

interface IClientService
{
    public function getAllClients();
    public function createClient(array $data);
    public function getClientById(int $id);
    public function updateClient(int $id, array $data);
    public function deleteClient(int $id);
    public function filteredClients(?string $compte, ?string $active);
}
