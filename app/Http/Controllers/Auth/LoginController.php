<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    // Redirige al panel después del login.
    protected $redirectTo = '/panel';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}