<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Visit;

class VisitController extends Controller
{
    /**
     * Display the user's visits.
     *
     * @return \Illuminate\View\View
     */
    public function myVisits()
    {
        $user = Auth::user();
        $visits = Visit::where('user_id', $user->id)->with(['specialist', 'facility'])->get();

        return view('my-visits', ['visits' => $visits]);
    }
}
