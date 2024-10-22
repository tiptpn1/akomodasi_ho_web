<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function login(AdminLoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('master_user_nama', $data['username'])->first();

        if (!$user) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "username or password wrong"
                    ]
                ]
            ], 401));
        } else {
            if (!password_verify($data['password'], $user->master_user_password)) {
                throw new HttpResponseException(response([
                    "errors" => [
                        "message" => [
                            "username or password wrong"
                        ]
                    ]
                ], 401));
            } else {
                Auth::login($user);

                return response()->json([
                    'success' => true,
                    'message' => 'Success login',
                    'data' => Auth::user()
                ]);
            }
        }
    }

    public function logout()
    {
        Auth::logout();
        Session::regenerate();

        return redirect()->route('home');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'data' => Auth::user()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
