<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PortalAuthController extends Controller
{
    public function create(Request $request): View
    {
        if ($request->filled('intended')) {
            $request->session()->put('url.intended', $request->string('intended')->toString());
        }

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Не получилось войти с этими данными.',
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        if ($user?->isAdmin()) {
            return redirect()->intended(url('/admin'));
        }

        $request->session()->forget('url.intended');

        return redirect()->route('dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('status', 'Вы вышли из аккаунта.');
    }

    public function google(): RedirectResponse
    {
        return redirect()
            ->route('login')
            ->with('status', 'Google-вход вынесен в интерфейс. Следующий шаг - подключить Socialite и OAuth credentials.');
    }
}
