<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Jobs\SendMailRegistered;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $this->validateRequest($request);
        // check if email already exists and return 400
        if ( $this->getUser($request) )
            return $this->response('Email already taken.', 400);

        $user = User::create([
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        SendMailRegistered::dispatch($user);

        return $this->response('User successfully registered.', 201);
    }

    public function login(Request $request)
    {
        $this->validateRequest($request);

        if (! auth()->attempt($request->all()) )
            return $this->response('Invalid credentials.', 400);

        $user = $this->getUser($request);

        return response()
            ->json([ 'access_token' => $user->createToken('User Token')->accessToken ], 201);
    }

    protected function validateRequest($request)
    {
        return $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
    }

    protected function response(string $message, int $status)
    {
        return response()
            ->json([ 'message' => $message ], $status);
    }

    protected function getUser(Request $request) : ?User
    {
        return User::where('email', $request->get('email'))->first();
    }
}
