<?php

namespace App\Http\Controllers\Dashboard\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard'; // ログイン後にリダイレクトされるURL

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:admins')->except('logout');
    }

    /*
     * ログインに関する認証
     */
    protected function guard()
    {
        return Auth::guard('admins');
    }

    /*
     * ログイン画面
     */
    public function showLoginForm()
    {
        return view('dashboard.auth.login');
    }

    /*
     * ログアウト後のリダイレクト先
     */
    public function loggedOut(Request $request)
    {
        return redirect('dashboard.login');
    }
}
