<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\TransactionRepositoryInterface;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;

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

            $transaction->update();

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

    protected function getTransactionStatusWithEndDate($transaction, $endDate)
    {
        // get transaction's payments
        $transactionPaymentsTotalAmount = $transaction->payment
            ->where('paid_on', '<', $endDate)->sum('amount');

        // calcualte the actual due amount
        $transactionDueAmount = $transaction->amount;

        if (!$transaction->is_VAT_inclusive) {
            $transactionDueAmount += $transaction->amount * ($transaction->VAT / 100);
        }

        if ($transactionPaymentsTotalAmount >= $transactionDueAmount) {
            // the transaction is paid
            return self::paid;
        } else if (Carbon::parse($endDate) > Carbon::parse($transaction->due_date)) {
            return self::overdue;
        }

        return self::outstanding;
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

    public function whereBetweenDates($startDate, $endDate)
    {
        // find all the transactions that happended between the given range
        $transactions =
        Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $paidAmount = 0;
        $outstandingAmount = 0;
        $overdueAmount = 0;

        if (count($transactions) == 0) {
            return [
                'paid' => $paidAmount,
                'outstanding' => $outstandingAmount,
                'overdue' => $overdueAmount,
            ];

        }

        $transactionPaymentsTotalAmount = 0;

        foreach ($transactions as $transaction) {
            // get transaction's payments
            $transactionPaymentsTotalAmount =
            $transaction->payment->where('paid_on', '<', $endDate)->sum('amount');

            $status_id =
            $this->getTransactionStatusWithEndDate($transaction, $endDate);

            if ($status_id == self::paid) {
                $paidAmount += $transactionPaymentsTotalAmount;
            } elseif ($status_id == self::outstanding) {
                $outstandingAmount += $transactionPaymentsTotalAmount;
            } elseif ($status_id == self::overdue) {
                $overdueAmount += $transactionPaymentsTotalAmount;
            }
        }

        return [
            'paid' => $paidAmount,
            'outstanding' => $outstandingAmount,
            'overdue' => $overdueAmount,
        ];
    }

    public function monthlyReport($startDate, $endDate)
    {
        $reportResults = array();

        $start = new DateTime($startDate);

        $start->modify('first day of this month');

        $end = new DateTime($endDate);

        $end->modify('last day of next month');

        $interval = DateInterval::createFromDateString('1 month');

        $periods = new DatePeriod($start, $interval, $end);

        $endDateTime = new DateTime($endDate);

        foreach ($periods as $period) {
            if ($period->format("Y-m-01") <=
                $endDateTime->format("Y-m-01")) {

                $monthResult = array();

                $monthResult['month'] = $period->format("m");

                $monthResult['year'] = $period->format("Y");

                $monthTransactionsAmounts =
                $this->whereBetweenDates($period->format("Y-m-01"),
                    $period->modify('+1 month')->format("Y-m-01"));

                $monthResult['paid'] = $monthTransactionsAmounts['paid'];

                $monthResult['outstanding'] = $monthTransactionsAmounts['outstanding'];

                $monthResult['overdue'] = $monthTransactionsAmounts['overdue'];

                array_push($reportResults, $monthResult);
            }
        }

        return $reportResults;
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