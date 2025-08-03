<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private function _logout(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user) {
            $user->setLastActivity('Logout');
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function login(Request $request)
    {
        if ($request->getMethod() === 'GET') {
            return inertia('app/auth/Login');
        }

        // kode dibawah ini untuk handle post

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ], [
            'email.required' => 'Alamat email harus diisi.',
            'password.required' => 'Kata sandi harus diisi.',
        ]);

        // basic validations
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        // extra validations
        $data = $request->only(['email', 'password']);

        if (!Auth::attempt($data, $request->has('remember'))) {
            $validator->errors()->add('email', 'Alamat email atau kata sandi salah!');
        } else if (!Auth::user()->active) {
            $validator->errors()->add('email', 'Akun anda tidak aktif. Silahkan hubungi penyedia layanan!');
            $this->_logout($request);
        } else {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user->setLastLogin();
            $user->setLastActivity('Login');
            $request->session()->regenerate();
            return redirect(route('app.dashboard'));
        }

        return redirect()->back()->withInput()->withErrors($validator);
    }

    public function logout(Request $request)
    {
        $this->_logout($request);
        return redirect('/')->with('success', 'Anda telah logout.');
    }
}
