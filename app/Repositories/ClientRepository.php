<?php
namespace App\Repositories;

use App\Http\Requests\TelephoneRequest;
use App\Models\Client;
use App\Repositories\Contracts\IClientRepository ;

class ClientRepository implements IClientRepository
{
    public function getAll()
    {
        return Client::all();
    }

    public function find(int $id)
    {
        return Client::findOrFail($id);
    }

    public function create(array $data)
    {
        return Client::create($data);
    }

    public function update(int $id, array $data)
    {
        $client = Client::findOrFail($id);
        $client->update($data);
        return $client;
    }

    public function delete(int $id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        return true;
    }

    public function findByLibelle(string $libelle)
    {
        return Client::where('libelle', $libelle)->first();
    }

    public function findByEtat(string $etat)
    {
        return Client::where('etat', $etat)->get();
    }

    public function findByTelephone(String $telephone)
    {
       return   Client::first();
    }
}
