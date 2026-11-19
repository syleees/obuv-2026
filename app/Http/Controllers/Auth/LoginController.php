<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
class LoginController extends Controller
{
protected $redirectTo = '/';

public function login(Request $request)
{
    $validated = $request->validate([
        'login' => ['required', 'string', 'max:100'],
    ]);

    $user = User::where('login', $validated['login'])
        ->first();

    if (!$user) {
        throw ValidationException::withMessages([
            'login' => 'Пользователь с таким логином не найден.',
        ]);
    }

    Auth::login($user);

    return redirect()->intended(route('catalog'))
        ->with('success', 'Добро пожаловать, ' . $user->full_name . '!');
}

public function username()
{
    return 'login';
}

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
