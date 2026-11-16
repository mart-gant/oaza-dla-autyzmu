<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FacilityResource;
use App\Models\Facility;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    /**
     * Display a listing of facilities.
     */
    public function index(Request $request)
    {
        $query = Facility::with(['user', 'reviews'])->withCount('reviews')->withAvg('reviews', 'rating');

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by city
        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // Search by name or description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Only verified facilities
        if ($request->boolean('verified')) {
            $query->where('is_verified', true);
        }

        $facilities = $query->paginate(min(max((int) $request->get('per_page', 15), 1), 100));

        return FacilityResource::collection($facilities);
    }

    /**
     * Display the specified facility.
     */
    public function show(Facility $facility)
    {
        $facility->load(['user', 'reviews.user']);
        
        return new FacilityResource($facility);
    }

    /**
     * Store a newly created facility.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:szkola,przedszkole,osrodek_terapeutyczny,poradnia,fundacja,stowarzyszenie,inne',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'services' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $facility = auth('api')->user()->facilities()->create($validated);

        return new FacilityResource($facility);
    }

    /**
     * Update the specified facility.
     */
    public function update(Request $request, Facility $facility)
    {
        // Check if user owns the facility or is admin
        if ($facility->user_id !== auth('api')->id() && !auth('api')->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:szkola,przedszkole,osrodek_terapeutyczny,poradnia,fundacja,stowarzyszenie,inne',
            'address' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'services' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $facility->update($validated);

        return new FacilityResource($facility);
    }

    /**
     * Remove the specified facility.
     */
    public function destroy(Facility $facility)
    {
        // Check if user owns the facility or is admin
        if ($facility->user_id !== auth('api')->id() && !auth('api')->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $facility->delete();

        return response()->json([
            'success' => true,
            'message' => 'Facility deleted successfully'
        ]);
    }
}
