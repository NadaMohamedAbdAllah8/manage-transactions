<?php

namespace App\Http\Controllers;

use App\Repositories\PaymentRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    private $paymentRepository;

    public function __construct(PaymentRepositoryInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|exists:transactions,id',
            'amount' => 'required',
            'paid_on' => 'required|date_format:Y-m-d H:i:s',
            'method_id' => 'exists:payment_methods,id',

        ], [
            'required' => 'The :attribute field is required.',
            'transaction_id.exists' => 'The :attribute field has to be a valid transaction id.',
            'method_id.exists' => 'The :attribute field has to be a valid payment method.',
            'paid_on.date_format' => 'Enter a valid time stamp in :attribute field',
        ]);

        if ($validator->fails()) {
            // get all errors
            $errors = $validator->errors()->all();

            return response()->json([
                "success" => false,
                "message" => "Validation Error",
                "title" => $errors,
            ]);
        }

        try {
            return $this->paymentRepository->create($request->all());
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error",
                "title" => $e->getMessage(),
            ]);
        }
    }
}
