<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\DayTour;
use Illuminate\Http\Request;

class DayTourController extends Controller
{
    /**
     * GET /api/countries/{slug}/day-tours
     * List day tours for a country with optional filters.
     */
    public function byCountry(Request $request, $slug)
    {
        $country = Country::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $query = DayTour::where('country_id', $country->id)->where('is_active', true);

        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $sort = $request->get('sort', 'popular');
        match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'rating'     => $query->orderBy('rating', 'desc'),
            default      => $query->orderBy('reviews_count', 'desc'),
        };

        return response()->json($query->get());
    }

    /**
     * GET /api/tours/{slug}
     * Return full tour detail.
     */
    public function show($slug)
    {
        $tour = DayTour::where('slug', $slug)
            ->where('is_active', true)
            ->with('country:id,name,slug', 'destination:id,name,slug')
            ->firstOrFail();

        return response()->json($tour);
    }

    // ─── Admin Routes ──────────────────────────────────────────────────────────

    /** GET /api/admin/day-tours */
    public function index()
    {
        return response()->json(
            DayTour::with('country:id,name,slug')->orderBy('title')->get()
        );
    }

    /** POST /api/admin/day-tours */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id'     => 'required|exists:countries,id',
            'destination_id' => 'nullable|exists:destinations,id',
            'slug'           => 'required|string|max:150|unique:day_tours',
            'title'          => 'required|string|max:255',
            'location'       => 'nullable|string|max:100',
            'image'          => 'nullable|string',
            'badge'          => 'nullable|string|max:50',
            'duration'       => 'nullable|string|max:50',
            'description'    => 'nullable|string',
            'features'       => 'nullable|array',
            'inclusions'     => 'nullable|array',
            'exclusions'     => 'nullable|array',
            'itinerary'      => 'nullable|array',
            'rating'         => 'nullable|numeric|min:0|max:5',
            'reviews_count'  => 'nullable|integer',
            'price'          => 'required|numeric|min:0',
            'currency'       => 'nullable|string|max:10',
            'is_active'      => 'boolean',
        ]);

        $tour = DayTour::create($validated);
        return response()->json($tour, 201);
    }

    /** PUT /api/admin/day-tours/{id} */
    public function update(Request $request, $id)
    {
        $tour = DayTour::findOrFail($id);

        $validated = $request->validate([
            'title'         => 'sometimes|string|max:255',
            'location'      => 'nullable|string|max:100',
            'image'         => 'nullable|string',
            'badge'         => 'nullable|string|max:50',
            'duration'      => 'nullable|string|max:50',
            'description'   => 'nullable|string',
            'features'      => 'nullable|array',
            'inclusions'    => 'nullable|array',
            'exclusions'    => 'nullable|array',
            'itinerary'     => 'nullable|array',
            'rating'        => 'nullable|numeric|min:0|max:5',
            'reviews_count' => 'nullable|integer',
            'price'         => 'sometimes|numeric|min:0',
            'currency'      => 'nullable|string|max:10',
            'is_active'     => 'boolean',
        ]);

        $tour->update($validated);
        return response()->json(['message' => 'Day tour updated.', 'tour' => $tour]);
    }

    /** DELETE /api/admin/day-tours/{id} */
    public function destroy($id)
    {
        DayTour::findOrFail($id)->delete();
        return response()->json(['message' => 'Day tour deleted.']);
    }
}
