<?php

use App\Livewire\Mobile\EmailVerification;
use App\Livewire\Mobile\ForgotPassword;
use App\Livewire\Mobile\Login;
use App\Livewire\Mobile\Register;
use App\Livewire\Mobile\ResetPassword;
use Livewire\Livewire;

test('login validates required credentials', function (): void {
    Livewire::test(Login::class)
        ->call('login')
        ->assertHasErrors([
            'email' => 'required',
            'password' => 'required',
        ])
        ->assertSet('toastMessage', 'Fix the highlighted sign-in fields.')
        ->assertSet('toastVariant', 'error');
});

test('login accepts valid credentials format', function (): void {
    $component = Livewire::test(Login::class);

    expect($component->instance()->canSubmit())->toBeFalse();

    $component
        ->set('email', 'person@example.com')
        ->set('password', 'password')
        ->set('remember', true)
        ->assertHasNoErrors();

    expect($component->instance()->canSubmit())->toBeTrue();

    $component
        ->call('login')
        ->assertHasNoErrors()
        ->assertSet('password', '')
        ->assertSet('status', 'Sign-in details validated.')
        ->assertSet('toastMessage', 'Sign-in details validated.')
        ->assertSet('toastVariant', 'success');
});

test('login validates email in real time', function (): void {
    Livewire::test(Login::class)
        ->set('email', 'not-an-email')
        ->assertHasErrors(['email' => 'email']);
});

test('register validates account fields', function (): void {
    Livewire::test(Register::class)
        ->call('register')
        ->assertHasErrors([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'termsAccepted' => 'accepted',
        ])
        ->assertSet('toastMessage', 'Fix the highlighted account fields.')
        ->assertSet('toastVariant', 'error');
});

test('register accepts valid account details', function (): void {
    $component = Livewire::test(Register::class);

    expect($component->instance()->canSubmit())->toBeFalse();

    $component
        ->set('name', 'Mobile User')
        ->set('email', 'mobile@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('termsAccepted', true)
        ->assertHasNoErrors();

    expect($component->instance()->canSubmit())->toBeTrue();

    $component
        ->call('register')
        ->assertHasNoErrors()
        ->assertSet('password', '')
        ->assertSet('password_confirmation', '')
        ->assertSet('status', 'Account details validated.')
        ->assertSet('toastMessage', 'Account details validated.')
        ->assertSet('toastVariant', 'success');
});

test('register validates email and password confirmation in real time', function (): void {
    Livewire::test(Register::class)
        ->set('email', 'not-an-email')
        ->assertHasErrors(['email' => 'email'])
        ->set('password', 'password')
        ->set('password_confirmation', 'different')
        ->assertHasErrors(['password_confirmation' => 'same']);
});

test('forgot password validates email before continuing', function (): void {
    $component = Livewire::test(ForgotPassword::class);

    expect($component->instance()->canSubmit())->toBeFalse();

    $component
        ->call('sendResetLink')
        ->assertHasErrors(['email' => 'required'])
        ->assertSet('toastMessage', 'Enter a valid email before continuing.')
        ->assertSet('toastVariant', 'error')
        ->set('email', 'reset@example.com')
        ->assertHasNoErrors();

    expect($component->instance()->canSubmit())->toBeTrue();

    $component
        ->call('sendResetLink')
        ->assertHasNoErrors()
        ->assertSet('status', 'Password reset instructions are ready to send.')
        ->assertSet('toastMessage', 'Password reset instructions are ready to send.')
        ->assertSet('toastVariant', 'success');
});

test('forgot password validates email in real time', function (): void {
    Livewire::test(ForgotPassword::class)
        ->set('email', 'not-an-email')
        ->assertHasErrors(['email' => 'email']);
});

test('reset password validates token email and confirmation', function (): void {
    $component = Livewire::test(ResetPassword::class);

    expect($component->instance()->canSubmit())->toBeFalse();

    $component
        ->call('resetPassword')
        ->assertHasErrors([
            'token' => 'required',
            'email' => 'required',
            'password' => 'required',
        ])
        ->assertSet('toastMessage', 'Fix the highlighted password reset fields.')
        ->assertSet('toastVariant', 'error')
        ->set('token', 'reset-token')
        ->set('email', 'reset@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->assertHasNoErrors();

    expect($component->instance()->canSubmit())->toBeTrue();

    $component
        ->call('resetPassword')
        ->assertHasNoErrors()
        ->assertSet('password', '')
        ->assertSet('password_confirmation', '')
        ->assertSet('status', 'New password details validated.')
        ->assertSet('toastMessage', 'New password details validated.')
        ->assertSet('toastVariant', 'success');
});

test('reset password validates email and confirmation in real time', function (): void {
    Livewire::test(ResetPassword::class)
        ->set('email', 'not-an-email')
        ->assertHasErrors(['email' => 'email'])
        ->set('password', 'password')
        ->set('password_confirmation', 'different')
        ->assertHasErrors(['password_confirmation' => 'same']);
});

test('email verification validates email before continuing', function (): void {
    Livewire::test(EmailVerification::class)
        ->call('sendVerification')
        ->assertHasErrors(['email' => 'required'])
        ->set('email', 'verify@example.com')
        ->call('sendVerification')
        ->assertHasNoErrors()
        ->assertSet('status', 'Verification email details validated.');
});
