<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\IUserRepository;
use App\Repositories\Eloquent\BaseRepository;

class UserRepository  implements IUserRepository
{

   public function create(array $data)
   {
       return User::create($data);
   }

   public function update(int $id, array $data)
   {
       return User::find($id)->update($data);
   }

   public function delete(int $id)
   {
       return User::find($id)->delete();
   }

   public function find(int $id)
   {
       return User::findOrFail($id);
   }

   public function getAll()
   {
       return User::all();
   }

   public function query( ) {
       return User::query();
   }

   public function filter($roleId = null, $etat = null)
    {
        $query = $this->query();

        if ($roleId !== null) {
            $query->where('role_id', $roleId);
        }

        if ($etat !== null) {
            $query->where('etat', $etat);
        }

        // dd($query->get());
        return $query->get();
    }








}
