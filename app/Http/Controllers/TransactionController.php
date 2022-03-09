<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
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
            'due_date' => 'required',
            'VAT' => 'required',
            'is_VAT_inclusive' => 'required',

        ], [
            'required' => 'The :attribute field is required.',
            'category_id.exists' => 'The :attribute field has to be a valid category id.',
            'sub_category_id.exists' =>
            'The :attribute field has to be a valid subcategory id, and belongs to the right category.',
        ]);

        if ($validator->fails()) {
            // get all errors 
            $errors = $validator->errors()->all();

            return response()->json([
                "success" => false,
                "message" => "Validation Error",
                "title" => $errors
            ]);
        }

        // 'status_id'
        try {
            return Transaction::create($request->all());
        } catch (Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Validation Error",
                "title" => $e->getMessage()
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
        return Transaction::find($id);
    }
}