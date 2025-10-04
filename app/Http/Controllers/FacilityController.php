<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facility;
use Illuminate\Support\Facades\Auth;

class FacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $facilities = Facility::paginate(15);
        return view('facilities.index', compact('facilities'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('facilities.create');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ]);
        
        $facility = new Facility($request->all());
        $facility->user_id = Auth::id();
        $facility->save();
        
        return redirect()->route('facilities.show', $facility)
            ->with('success', 'Placówka została pomyślnie utworzona.');
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Facility  $facility
     * @return \Illuminate\Http\Response
     */
    public function show(Facility $facility)
    {
        return view('facilities.show', compact('facility'));
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Facility  $facility
     * @return \Illuminate\Http\Response
     */
    public function edit(Facility $facility)
    {
        return view('facilities.edit', compact('facility'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Facility  $facility
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Facility $facility)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ]);
        
        $facility->update($request->all());
        
        return redirect()->route('facilities.show', $facility)
            ->with('success', 'Dane placówki zostały pomyślnie zaktualizowane.');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Facility  $facility
     * @return \Illuminate\Http\Response
     */
    public function destroy(Facility $facility)
    {
        $facility->delete();
        
        return redirect()->route('facilities.index')
            ->with('success', 'Placówka została pomyślnie usunięta.');
    }
}
