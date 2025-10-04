<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    /**
     * Wyświetl profil użytkownika.
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Formularz edycji profilu.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Aktualizacja profilu użytkownika.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'interests' => 'nullable|string|max:500',
            'support_preferences' => 'nullable|string|max:500',
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.show');
    }

    /**
     * Formularz zmiany hasła.
     */
    public function changePasswordForm()
    {
        return view('profile.change_password');
    }

    /**
     * Zmiana hasła użytkownika.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Sprawdzenie poprawności obecnego hasła
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('profile.change_password')->withErrors(['current_password' => 'Podane hasło jest nieprawidłowe.']);
        }

        // Sprawdzenie, czy nowe hasło nie jest takie samo jak stare
        if (Hash::check($request->new_password, $user->password)) {
            return redirect()->route('profile.change_password')->withErrors(['new_password' => 'Nowe hasło nie może być takie samo jak obecne.']);
        }

        try {
            $user->password = Hash::make($request->new_password);
            $user->save();

            return redirect()->route('profile.show')->with('success', 'Hasło zostało zmienione.');
        } catch (\Exception $e) {
            return redirect()->route('profile.change_password')->withErrors(['error' => 'Wystąpił błąd podczas zmiany hasła.'])->withInput();
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
