<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Models\User;
use App\Services\MobileAuth\MobileAuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

final class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login', ['title' => 'Admin Login']);
    }

    public function store(LoginRequest $request, MobileAuditLogger $audit): RedirectResponse
    {
        $validated = $request->validated();
        $user = User::query()
            ->select(['id', 'name', 'email', 'password', 'is_platform_admin'])
            ->where('email', $validated['email'])
            ->first();

        if ($user === null || ! $user->is_platform_admin || ! Hash::check($validated['password'], $user->password)) {
            $audit->record('admin_login_failed', $request, $user, severity: 'warning', metadata: [
                'email' => $validated['email'],
            ]);

            return back()
                ->withErrors(['email' => 'The provided admin credentials are invalid.'])
                ->onlyInput('email');
        }

        Auth::login($user);
        $request->session()->regenerate();

        $audit->record('admin_login_succeeded', $request, $user);

        return redirect()->intended(route('admin.dashboard'));
    }
}
