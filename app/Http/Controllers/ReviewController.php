<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Facility;

class ReviewController extends Controller
{
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
            'comment' => $validated['comment'] ? strip_tags($validated['comment']) : null,
        ]);
        
        return redirect()->route('facilities.show', $validated['facility_id'])->with('success', 'Dziękujemy za dodanie opinii!');
    }

    public function destroy(Review $review)
    {
        // Sprawdź, czy użytkownik jest właścicielem recenzji lub adminem
        if (auth()->id() !== $review->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $facilityId = $review->facility_id;
        $review->delete();

        return redirect()->route('facilities.show', $facilityId)->with('success', 'Opinia została usunięta.');
    }
}
