<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Repositories\PaymentRepositoryInterface;
use App\Repositories\TransactionRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    private $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function create($data)
    {
        $payment = Payment::create($data);

        $this->transactionRepository->updateTransactionStatus($data['transaction_id']);

        return $payment;
    }
}
