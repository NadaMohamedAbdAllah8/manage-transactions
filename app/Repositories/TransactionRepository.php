<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\TransactionRepositoryInterface;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function findById($id)
    {
        return Transaction::where('id', $id)->get()
            ->map(
                function ($transaction) {
                    return $this->formatResult($transaction);
                }
            );
    }

    protected function formatResult($transaction)
    {
        return [
            'Id' => $transaction->id,
            'Payer' => $transaction->customer->name,
            'Category' => $transaction->category->name,
            'Subcategory' => ($transaction->subCategory->name) ?? 'Does not have a sub category',
            'Amount' => $transaction->amount,
            'Status' => $transaction->transactionStatus->name,
            'Due date' => $transaction->due_date->diffForHumans(),
            'Due date timestamp' => $transaction->due_date,
        ];

    }
}
