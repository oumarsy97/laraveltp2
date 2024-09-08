<?php

namespace App\Services\Contracts;

interface IUserService
{

    public function index( $role = null, $active = null);

    public function find(int $id);

    public function create(array $data);

    public function update(array $data,int $id);

    public function delete(int $id);
}
