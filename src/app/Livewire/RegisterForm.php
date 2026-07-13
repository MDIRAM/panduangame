<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class RegisterForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    
    // Captcha variables
    public int $num1;
    public int $num2;
    public string $captcha_answer = '';

    public function mount(): void
    {
        $this->generateCaptcha();
    }

    public function generateCaptcha(): void
    {
        $this->num1 = random_int(1, 9);
        $this->num2 = random_int(1, 9);
        $this->captcha_answer = '';
    }

    public function register(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'captcha_answer' => ['required', 'numeric'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'captcha_answer.required' => 'Jawaban Captcha wajib diisi.',
            'captcha_answer.numeric' => 'Jawaban Captcha harus berupa angka.',
        ]);

        // Validate Math Captcha
        if ((int)$this->captcha_answer !== ($this->num1 + $this->num2)) {
            $this->addError('captcha_answer', 'Jawaban Captcha salah. Silakan coba lagi.');
            $this->generateCaptcha();
            return;
        }

        // Simpan ke database
        $user = DB::transaction(function () {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            // Assign role 'member'
            $memberRole = Role::firstOrCreate([
                'name' => 'member',
                'guard_name' => 'web',
            ]);
            $user->assignRole($memberRole);

            return $user;
        });

        // Login-kan user
        Auth::login($user);
        session()->regenerate();

        $this->redirect(route('home'), navigate: true);
    }

    public function render()
    {
        return view('livewire.register-form');
    }
}
