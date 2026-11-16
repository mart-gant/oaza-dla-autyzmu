<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Search users by name or email
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json(['users' => []]);
        }

        $users = User::where('id', '!=', auth()->id())
            ->where('is_suspended', false)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->select('id', 'name') // bez e-maili: każdy zalogowany mógłby zebrać adresy wszystkich użytkowników
            ->limit(10)
            ->get();

        return response()->json(['users' => $users]);
    }
}
