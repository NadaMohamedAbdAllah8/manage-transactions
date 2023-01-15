<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SubCategory::all();
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
            'name' => 'required|unique:sub_categories,name',
            'category_id' => 'required|exists:categories,id'
        ], [
            'name.required' => 'The :attribute field is required.',
            'name.unique' => 'The :attribute field has to be unique.',
            'category_id.required' => 'The :attribute field is required.',
            'category_id.exists' => 'The :attribute field has to be a valid category id.',
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

        return SubCategory::create($request->all());
    }
}
