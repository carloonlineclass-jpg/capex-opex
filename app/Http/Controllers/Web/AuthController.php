<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    private const REGISTRATION_CODE_KEY = 'registration_code';
    private const REGISTRATION_VERIFIED_KEY = 'registration_verified_email';

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister(Request $request)
    {
        return view('auth.register', [
            'verifiedEmail' => $request->session()->get(self::REGISTRATION_VERIFIED_KEY),
            'pendingVerification' => $request->session()->get(self::REGISTRATION_CODE_KEY),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

   public function sendVerificationCode(Request $request)
{
    $validated = $request->validate([
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
    ]);

    $code = (string) random_int(100000, 999999);

    $request->session()->put(self::REGISTRATION_CODE_KEY, [
        'email' => $validated['email'],
        'code_hash' => Hash::make($code),
        'expires_at' => now()->addMinutes(10)->timestamp,
    ]);

    $request->session()->forget(self::REGISTRATION_VERIFIED_KEY);

    try {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'api-key' => env('BREVO_API_KEY'),
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'name' => env('MAIL_FROM_NAME', 'Capex Opex'),
                'email' => env('MAIL_FROM_ADDRESS', 'carloonlineclass@gmail.com'),
            ],
            'to' => [
                ['email' => $validated['email']],
            ],
            'subject' => 'Your Account Verification Code',
            'textContent' => "Your verification code is: {$code}\n\nThis code will expire in 10 minutes.",
        ]);

        if (! $response->successful()) {
            Log::error('BREVO API ERROR: '.$response->body());

            return back()->with('error', 'Email failed. Your code is: '.$code)
                ->withInput([
                    'email' => $validated['email'],
                ]);
        }
    } catch (\Throwable $e) {
        Log::error('BREVO API EXCEPTION: '.$e->getMessage());

        return back()->with('error', 'Email failed. Your code is: '.$code)
            ->withInput([
                'email' => $validated['email'],
            ]);
    }

    return back()->with('success', 'Verification code sent to your email.')->withInput([
        'email' => $validated['email'],
    ]);
}

    public function verifyCode(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
        ]);

        $verification = $request->session()->get(self::REGISTRATION_CODE_KEY);

        if (!$verification || ($verification['email'] ?? null) !== $validated['email']) {
            return back()->withErrors(['email' => 'No verification request found for this email.'])->withInput();
        }

        if (now()->timestamp > ($verification['expires_at'] ?? 0)) {
            $request->session()->forget(self::REGISTRATION_CODE_KEY);
            return back()->withErrors(['code' => 'Verification code expired. Please request a new code.'])->withInput();
        }

        if (!Hash::check($validated['code'], $verification['code_hash'] ?? '')) {
            return back()->withErrors(['code' => 'Invalid verification code.'])->withInput();
        }

        $request->session()->put(self::REGISTRATION_VERIFIED_KEY, $validated['email']);

        return back()->with('success', 'Email verified. You can now create your account.')->withInput([
            'verified_email' => $validated['email'],
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'department_id' => ['nullable', 'exists:departments,id'],
        ]);

        $verifiedEmail = $request->session()->get(self::REGISTRATION_VERIFIED_KEY);

        if ($verifiedEmail !== $validated['email']) {
            return back()->withErrors(['email' => 'Please verify this email address first.'])->withInput();
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::driver('bcrypt')->make($validated['password'], [
                'rounds' => (int) config('security.bcrypt_rounds', 12),
            ]),
            'department_id' => $validated['department_id'] ?? null,
            'role' => 'requestor',
            'is_approved' => false,
            'approved_at' => null,
            'email_verified_at' => now(),
        ]);

        User::where('role', 'admin')->get()->each(function (User $admin) use ($user) {
            $admin->notify(new class($user) extends \Illuminate\Notifications\Notification {
                public function __construct(private User $newUser) {}
                public function via(object $notifiable): array { return ['database']; }
                public function toArray(object $notifiable): array {
                    return [
                        'subject' => 'New user registration pending review',
                        'message' => $this->newUser->name.' registered and is waiting for approval.',
                        'status_label' => 'Pending Review',
                        'requisition_no' => 'USER-'.str_pad((string)$this->newUser->id, 4, '0', STR_PAD_LEFT),
                        'url' => route('users.index'),
                    ];
                }
            });
        });

        $request->session()->forget([self::REGISTRATION_CODE_KEY, self::REGISTRATION_VERIFIED_KEY]);

        return redirect()->route('login')->with('success', 'Account created. Please wait for admin approval before logging in.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user && !$user->is_approved) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['email' => 'Your account is still pending admin approval.'])->onlyInput('email');
            }

            if ($user && Hash::needsRehash($user->password)) {
                $user->forceFill([
                    'password' => Hash::driver('bcrypt')->make($credentials['password'], [
                        'rounds' => (int) config('security.bcrypt_rounds', 12),
                    ]),
                ])->save();
            }

            return redirect()->route('dashboard')->with('success', 'Welcome back.');
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}