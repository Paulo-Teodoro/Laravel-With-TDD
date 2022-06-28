<?php

namespace App\Repository\Contracts;

interface UserRepositoryInterface
{
    public function find(string $email) :?object;
    public function findAll() :array;
    public function store(array $data) :object;
    public function update(string $email, array $data) :object;
    public function delete(string $email) :bool;
}