<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Masuk Akun - Syafa Frozen Food')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = true;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:4',
    ];

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirectUser();
        }
    }

    public function fillAdmin(): void
    {
        $this->email = 'admin@syafafrozen.com';
        $this->password = 'admin123';
    }

    public function fillCashier(): void
    {
        $this->email = 'kasir@syafafrozen.com';
        $this->password = 'kasir123';
    }

    public function authenticate()
    {
        $this->validate();

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            throw ValidationException::withMessages([
                'email' => 'Kombinasi email atau password tidak sesuai.',
            ]);
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Akun Anda sedang dinonaktifkan. Hubungi admin.',
            ]);
        }

        session()->regenerate();

        $this->redirectUser();
    }

    private function redirectUser()
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return $this->redirectRoute('admin.dashboard', navigate: true);
        }

        return $this->redirectRoute('pos.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
