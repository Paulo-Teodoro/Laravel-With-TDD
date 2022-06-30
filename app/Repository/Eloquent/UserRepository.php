<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Repository\Contracts\PaginationInterface;
use App\Repository\Contracts\UserRepositoryInterface;
use App\Repository\Presenters\PaginationEloquentPresenter;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function find(string $email) :?object
    {
        return $this->model->where('email', $email)->first();
    }

    public function findAll() :array
    {
        return $this->model->get()->toArray();
    }

    public function paginate(int $page = 1) :PaginationInterface
    {
        return new PaginationEloquentPresenter($this->model->paginate());
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