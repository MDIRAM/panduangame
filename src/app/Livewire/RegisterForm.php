<?php

namespace App\Livewire;

use App\Mail\SendOtpMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class RegisterForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $otp_code = '';
    public string $password = '';
    public string $password_confirmation = '';
    public int $step = 1;
    public bool $otpSent = false;
    public string $statusMessage = '';

    protected array $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
    ];

    protected array $messages = [
        'name.required' => 'Nama lengkap wajib diisi.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email ini sudah terdaftar.',
    ];

    public function sendOtp(): void
    {
        $this->validate();

        // Bersihkan registrasi tertunda lama untuk email ini
        DB::table('pending_registrations')->where('email', $this->email)->delete();

        // Generate 4-digit OTP code
        $otpCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        // Simpan ke database
        DB::table('pending_registrations')->insert([
            'name' => $this->name,
            'email' => $this->email,
            'otp_code' => $otpCode,
            'expires_at' => Carbon::now()->addMinutes(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Kirim email OTP
        try {
            Mail::to($this->email)->send(new SendOtpMail($otpCode));
            $this->otpSent = true;
            $this->step = 2;
            $this->statusMessage = 'Kode OTP berhasil dikirim ke email Anda.';
        } catch (\Exception $e) {
            $this->addError('email', 'Gagal mengirim email OTP. Silakan periksa koneksi internet / SMTP Anda.');
        }
    }

    public function register(): void
    {
        if ($this->step < 2) {
            return;
        }

        $this->validate([
            'otp_code' => ['required', 'string', 'size:4'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'otp_code.required' => 'Kode OTP wajib diisi.',
            'otp_code.size' => 'Kode OTP harus 4 digit.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $pending = DB::table('pending_registrations')
            ->where('email', $this->email)
            ->first();

        if (!$pending || $pending->otp_code !== $this->otp_code || Carbon::parse($pending->expires_at)->isPast()) {
            $this->addError('otp_code', 'Kode OTP salah, tidak cocok, atau sudah kedaluwarsa.');
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

            // Bersihkan data pending
            DB::table('pending_registrations')->where('email', $this->email)->delete();

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
