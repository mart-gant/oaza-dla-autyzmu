<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Facility;

class ReviewController extends Controller
{
    public function index($facility_id)
    {
        $facility = Facility::findOrFail($facility_id);
        $reviews = Review::where('facility_id', $facility_id)->get();
        
        return view('reviews.index', compact('facility', 'reviews'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);
        
        Review::create([
            'user_id' => auth()->id(),
            'facility_id' => $validated['facility_id'],
            'rating' => $validated['rating'],
            'comment' => strip_tags($validated['comment']),
        ]);
        
        return redirect()->route('reviews.index', $validated['facility_id'])->with('success', 'Recenzja dodana.');
    }
}
