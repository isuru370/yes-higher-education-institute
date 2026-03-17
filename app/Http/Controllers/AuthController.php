<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends Controller
{

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        return $this->authService->login($request);
    }

    public function user(Request $request)
    {
        return $this->authService->user($request);
    }

    public function profile(Request $request)
    {
        return $this->authService->profile($request);
    }

    public function logout(Request $request)
    {
        return $this->authService->logout($request);
    }



    // Web login form display
    public function showLoginForm()
    {
        return view('auth.login');
    }
}
