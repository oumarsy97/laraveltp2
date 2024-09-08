<?php
namespace App\Repositories;

use App\Enums\EtatEnum;
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

    public function findWithUser(int $id)
    {
        return Client::with('user')->findOrFail($id);
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

    public function query()
    {
        return Client::query();
    }

    public function filterByUser($query, $user)
    {
        if ($user == 1) {
            $query->whereNotNull('user_id')->with('user');
        } elseif ($user == 0) {
            $query->whereNull('user_id');
        }

        return $query;
    }

    public function filterByEtat($query, $etat)
    {
        if ($etat === EtatEnum::ACTIF->value) {
            $query->whereHas('user', function ($query) {
                $query->where('etat', '=', EtatEnum::ACTIF->value);
            })->with('user');
        } elseif ($etat === EtatEnum::INACTIF->value) {
            $query->whereHas('user', function ($query) {
                $query->where('etat', '=', EtatEnum::INACTIF->value);
            })->with('user');
        }

        return $query;
    }
}
