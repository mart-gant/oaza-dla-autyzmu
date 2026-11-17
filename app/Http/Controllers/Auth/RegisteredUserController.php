<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', new \App\Rules\StrongPassword()],
            'role' => ['required', 'string', 'in:autistic_person,parent,therapist,educator'],
            'terms' => ['accepted'],
        ]);

        // Determine if user is a specialist based on role
        $isSpecialist = in_array($request->role, ['therapist', 'educator']);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'profile_photo' => null,
            'interests' => '',
            'support_preferences' => '',
            'is_specialist' => $isSpecialist,
            'specialization' => $isSpecialist ? '' : null,
            'description' => $isSpecialist ? '' : null,
        ]);

        // Awaria poczty nie może zostawić użytkownika z błędem 500 przy już utworzonym koncie;
        // link weryfikacyjny da się wysłać ponownie z ekranu "potwierdź e-mail".
        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            report($e);
        }

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
