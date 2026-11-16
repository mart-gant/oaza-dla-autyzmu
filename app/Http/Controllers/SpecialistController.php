<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SpecialistController extends Controller
{
    public function index()
    {
        $specialists = User::where('is_specialist', true)->paginate(15);
        return view('specialists.index', ['specialists' => $specialists]);
    }

    public function create()
    {
        if (Auth::user()?->role !== 'admin') {
            abort(403);
        }

        return view('specialists.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()?->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $specialist = User::create([
            'name' => $request->name,
            'email' => Str::slug($request->name) . '-' . time() . '@example.com',
            // Profil bez logowania: nikt nie zna tego hasła
            'password' => Hash::make(Str::random(40)),
            'is_specialist' => true,
            'specialization' => $request->specialization,
            'description' => $request->description,
        ]);

        return redirect()->route('specialists.show', $specialist->id)
            ->with('success', 'Profil specjalisty został pomyślnie utworzony.');
    }

    public function show($id)
    {
        $specialist = User::where('is_specialist', true)->findOrFail($id);
        return view('specialists.show', ['specialist' => $specialist]);
    }

    public function edit($id)
    {
        if (Auth::user()?->role !== 'admin') {
            abort(403);
        }

        $specialist = User::where('is_specialist', true)->findOrFail($id);
        return view('specialists.edit', ['specialist' => $specialist]);
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()?->role !== 'admin') {
            abort(403);
        }

        $specialist = User::where('is_specialist', true)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $specialist->update([
            'name' => $request->name,
            'specialization' => $request->specialization,
            'description' => $request->description,
        ]);

        return redirect()->route('specialists.show', $specialist->id)
            ->with('success', 'Profil specjalisty został pomyślnie zaktualizowany.');
    }

    public function destroy($id)
    {
        if (Auth::user()?->role !== 'admin') {
            abort(403);
        }

        $specialist = User::where('is_specialist', true)->findOrFail($id);
        $specialist->delete();

        return redirect()->route('specialists.index')
            ->with('success', 'Profil specjalisty został pomyślnie usunięty.');
    }
}
