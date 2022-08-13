<?php

namespace App\Http\Services;

use App\Http\Repositories\UserRepository;

class UserService
{
    protected $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository  = $repository;
    }

    public function store($request)
    {
        return $this->repository->store($request);
    }

    public function update($request, $email)
    {
        return $this->repository->update($request, $email);
    }

    public function getUserByEmail($email)
    {
        return $this->repository->getUserByEmail($email);
    }
}
