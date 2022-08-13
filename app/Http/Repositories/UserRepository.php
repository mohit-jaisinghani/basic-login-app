<?php

namespace App\Http\Repositories;

use App\Models\User;

class UserRepository
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function store($request)
    {
        $row = new $this->model($request);
        return $row->save();
    }

    public function update($request, $email)
    {
       $record = $this->model->where('email', $email)->first();
       return $record->update($request);
    }

    public function getUserByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }
}
