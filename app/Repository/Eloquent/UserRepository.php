<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Repository\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function findAll() :array
    {
        return $this->model->get()->toArray();
    }

    public function store(array $data) :object
    {
        return $this->model->create($data);
    }

    public function update(string $email, array $data) :object
    {
        $user = $this->model->where('email', $email)->firstOrFail();
        $user->update($data);

        return $user;
    }

    public function delete(string $email) :bool
    {
        $user = $this->model->where('email', $email)->firstOrFail();

        return $user->delete();
    }
}