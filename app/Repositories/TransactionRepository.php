<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\TransactionRepositoryInterface;
use Carbon\Carbon;

class TransactionRepository implements TransactionRepositoryInterface
{
    const paid = 1;
    const outstanding = 2;
    const overdue = 3;

    public function create($data)
    {
        $status_id = $this->getTransactionStatus($data['due_date']);

        $data['status_id'] = $status_id;

        $transaction = Transaction::create($data);

        return $transaction;
    }

    public function updateTransactionStatus($transaction_id)
    {
        $transaction = Transaction::where('id', $transaction_id)->first();

        // calcualte the actual due amount
        $transactionDueAmount = $transaction->amount;

        if (!$transaction->is_VAT_inclusive) {
            $transactionDueAmount += $transaction->amount * ($transaction->VAT / 100);
        }

        // get transaction's payments
        $transactionPaymentsTotalAmount = $transaction->payment->sum('amount');

        if ($transactionPaymentsTotalAmount >= $transactionDueAmount) {
            // the transaction is paid

            $transaction->status_id = self::paid;

            $transaction->update();

            return;
        } else {
            $status_id = $this->getTransactionStatus($transaction->due_date);

            $transaction->status_id = $status_id;

            return;
        }
    }

    protected function getTransactionStatus($transactionDueDate)
    {
        $status_id = self::outstanding;

        if (Carbon::now() > Carbon::parse($transactionDueDate)) {
            $status_id = self::overdue;
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

    public function findByCustomer($customer_id)
    {
        return Transaction::where('customer_id', $customer_id)->get()
            ->map(
                function ($transaction) {
                    return $this->formatResult($transaction);
                }
            );

    }

    public function findPayments($id)
    {
        $transaction = Transaction::where('id', $id)->first();

        return $transaction->payment
            ->map(
                function ($payment) {
                    return $this->formatPaymentResult($payment);
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

    protected function formatPaymentResult($payment)
    {
        return [
            'Id' => $payment->id,
            'Transaction id' => $payment->transaction_id,
            'Amount' => $payment->amount,
            'Payment method' => ($payment->paymentMethod->name) ?? 'Method is not set',
            'Paid at' => $payment->paid_on,
            'Due date timestamp' => $payment->paid_on,
            'Details' => $payment->details,
        ];
    }
}
