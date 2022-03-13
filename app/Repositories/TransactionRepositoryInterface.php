<?php

namespace App\Repositories;

interface TransactionRepositoryInterface
{
    public function create($data);

    public function findById($id);

    public function findByCustomer($customer_id);

    public function updateTransactionStatus($id);

    public function findPayments($id);

    public function whereBetweenDates($startingDate, $endingDate);

    public function monthlyReport($startingDate, $endingDate);
}