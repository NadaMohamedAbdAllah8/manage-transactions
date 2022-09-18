<?php

namespace App\Http\Controllers;

use App\Repositories\TransactionRepositoryInterface;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    private $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
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
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'exists:sub_categories,id,category_id,' . $request->get('category_id'),
            'amount' => 'required',
            'customer_id' => 'required|exists:customers,id',
            'due_date' => 'required|date_format:Y-m-d H:i:s',
            'VAT' => 'required',
            'is_VAT_inclusive' => 'required',
        ], [
            'required' => 'The :attribute field is required.',
            'category_id.exists' => 'The :attribute field has to be a valid category id.',
            'sub_category_id.exists' =>
            'The :attribute field has to be a valid subcategory id, and belongs to the right category.',
            'due_date.date_format' => 'Enter a valid time stamp in :attribute field',
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
            return $this->transactionRepository->create($request->all());
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error",
                "title" => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return $this->transactionRepository->findById($id);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error",
                "title" => $e->getMessage(),
            ]);
        }
    }

    // show the transaction's payment
    public function payments($id)
    {
        try {
            return $this->transactionRepository->findPayments($id);
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Error",
                "title" => $e->getMessage(),
            ]);
        }
    }

    // for the given range show total sum of paid, outstanding,
    // and overdue transactions
    public function rangeReport($startingDate, $endingDate)
    {
        $validRange = $this->validateRange($startingDate, $endingDate);

        if (!$validRange) {
            return response()->json([
                "success" => false,
                "message" => "Error",
                "title" => 'Enter valid dates in the correct format, ex. 2022-03-10',
            ]);
        }

        return $this->transactionRepository->whereBetweenDates($startingDate, $endingDate);
    }

    // for each month of the given range show total sum of paid, outstanding,
    // and overdue transactions
    public function monthlyReport($startingDate, $endingDate)
    {
        $validRange = $this->validateRange($startingDate, $endingDate, 'Y-m');

        if (!$validRange) {
            return response()->json([
                "success" => false,
                "message" => "Error",
                "title" => 'Enter valid dates in the correct format, ex. 2022-03',
            ]);
        }

        return $this->transactionRepository->monthlyReport($startingDate, $endingDate);
    }

    public function validateRange($startingDate, $endingDate, $format = 'Y-m-d')
    {
        if (is_null($startingDate) || is_null($endingDate)) {
            return false;
        }

        if (!$this->validateDate($startingDate, $format) ||
            !$this->validateDate($endingDate, $format)) {
            return false;
        }

        if ($startingDate > $endingDate) {
            return false;
        }

        return true;
    }

    public function validateDate($date, $format = 'Y-m-d')
    {
        $dateCheck = DateTime::createFromFormat($format, $date);

        return $dateCheck && $dateCheck->format($format) === $date;
    }

}