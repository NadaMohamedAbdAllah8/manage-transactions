<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\TransactionRepositoryInterface;
use Carbon\Carbon;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function create($data)
    {
        $status_id = $this->getTransactionStatus($data['due_date']);

        $data['status_id'] = $status_id;

        $transaction = Transaction::create($data);

        return $transaction;
    }

    protected function getTransactionStatus($transactionDueDate)
    {
        $status_id = 2;

        if (Carbon::now() > Carbon::parse($transactionDueDate)) {
            $status_id = 3;
        }

        return $status_id;
    }

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
