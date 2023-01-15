<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Repositories\TransactionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    private $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:customers,email',
            'password' => 'required|string|confirmed',
        ]);

        $customer = Customer::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
        ]);

        $token = $customer->createToken('myapptoken')->plainTextToken;

        $response = [
            'customer' => $customer,
            'token' => $token,
        ];

        return response($response, 201);
    }

    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Check email
        $customer = Customer::where('email', $fields['email'])->first();

        // Check password
        if (!$customer || !Hash::check($fields['password'], $customer->password)) {
            return response([
                'message' => 'Bad credentials',
            ], 401);
        }

        $token = $customer->createToken('myapptoken')->plainTextToken;

        $response = [
            'customer' => $customer,
            'token' => $token,
        ];

        return response($response, 201);
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->user()->tokens()->delete();

        return [
            'message' => 'Logged out',
        ];
    }

    public function transactions()
    { //return 'hi';
        return $this->transactionRepository->findByCustomer(Auth::guard('customer')->user()->id);
    }
}
