<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Laravel mengingat halaman terakhir yang dicoba saat belum login (url.intended),
        // misalnya dari tab lama atau riwayat browser. Kalau halaman itu khusus admin dan
        // yang login kasir, hasilnya 403. Tujuan seperti itu dibuang; tujuan lain tetap dihormati.
        if (! $user->isAdmin() && $this->khususAdmin($request->session()->get('url.intended'))) {
            $request->session()->forget('url.intended');
        }

        return redirect()->intended(route($user->routeAwal(), absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Apakah URL ini mengarah ke route yang dijaga middleware 'admin'?
     */
    private function khususAdmin(?string $url): bool
    {
        if (! $url) {
            return false;
        }

        try {
            $route = app('router')->getRoutes()->match(Request::create($url, 'GET'));
        } catch (\Throwable $e) {
            return false; // URL tidak dikenal: biarkan Laravel yang menanganinya
        }

        return in_array('admin', $route->gatherMiddleware(), true);
    }
}