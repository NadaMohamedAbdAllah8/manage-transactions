<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
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
            'name' => 'required|unique:categories,name'
        ], [
            'required' => 'The :attribute field is required.',
            'unique' => 'The :attribute field has to be unique.',
        ]);

        if ($validator->fails()) {
            // get all errors 
            $errors = $validator->errors()->all();

            return response()->json([
                "success" => false,
                "message" => "Validation Error",
                "title" => $errors // or $errors
            ]);
        }

        return Transaction::create($request->all());
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