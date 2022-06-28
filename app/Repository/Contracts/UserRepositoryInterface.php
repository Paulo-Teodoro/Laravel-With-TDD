<?php

namespace App\Repository\Contracts;

interface UserRepositoryInterface
{
    public function findAll() :array;
    public function store(array $data) :object;
}