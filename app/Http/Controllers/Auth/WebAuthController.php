<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WebAuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->safe()->only(['email', 'password']);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi tidak cocok.',
            ]);
        }

        $request->session()->regenerate();

        $destination = $request->user()->is_admin
            ? route('admin.products.index')
            : (! empty(session('cart')) ? route('checkout.create') : route('shop'));

        return redirect()->intended($destination);
    }

    public function createRegistration(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create($request->safe()->only(['name', 'email', 'password']));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route(! empty(session('cart')) ? 'checkout.create' : 'shop')
            ->with('status', 'Akun pelanggan berhasil dibuat. Silakan lanjutkan belanja.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
