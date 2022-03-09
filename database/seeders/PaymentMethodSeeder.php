<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::table('payment_methods')->count() == 0) {
            DB::table('payment_methods')->insert(
                [
                    array(
                        'id' => 1,
                        'name' => 'credit card',
                    ),
                    array(
                        'id' => 2,
                        'name' => 'cash',
                    ),
                    array(
                        'id' => 3,
                        'name' => 'bank transfer',
                    ),
                ]
            );
        }
    }
}