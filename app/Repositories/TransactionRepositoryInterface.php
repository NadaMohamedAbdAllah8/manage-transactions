<?php

namespace App\Repositories;

interface TransactionRepositoryInterface
{
    public function create($data);

    public function findById($id);
}
