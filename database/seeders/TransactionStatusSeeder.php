<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::table('transaction_statuses')->count() == 0) {
            DB::table('transaction_statuses')->insert(
                [
                    array(
                        'id' => 1,
                        'name' => 'paid',
                    ),
                    array(
                        'id' => 2,
                        'name' => 'outstanding',
                    ),
                    array(
                        'id' => 3,
                        'name' => 'overdue',
                    ),
                ]
            );
        }
    }
}