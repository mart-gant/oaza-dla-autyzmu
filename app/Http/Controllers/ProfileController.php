<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Wymaga logowania do każdej akcji w kontrolerze
    }
    
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
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'profile_picture' => 'nullable|image|max:2048',
            'interests' => 'nullable|string|max:500',
            'support_preferences' => 'nullable|string|max:500',
        ]);
        
        try {
            // Aktualizacja danych użytkownika
            $user->name = strip_tags($validated['name']);
            $user->email = strip_tags($validated['email']);
            $user->interests = !empty($validated['interests']) ? strip_tags($validated['interests']) : $user->interests;
            $user->support_preferences = !empty($validated['support_preferences']) ? strip_tags($validated['support_preferences']) : $user->support_preferences;
            
            // Obsługa zdjęcia profilowego
            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture && Storage::exists($user->profile_picture)) {
                    Storage::delete($user->profile_picture);
                }
                $path = $request->file('profile_picture')->store('profile_pictures');
                $user->profile_picture = $path;
            }
            
            $user->save();
            
            return redirect()->route('profile.show')->with('success', 'Profil został zaktualizowany.');
        } catch (\Exception $e) {
            return redirect()->route('profile.edit')->withErrors(['error' => 'Wystąpił błąd podczas aktualizacji.'])->withInput();
        }
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
}
