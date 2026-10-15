<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    /**
     * GET /api/countries/{slug}/hotels
     * List hotels for a country with optional filters.
     */
    public function byCountry(Request $request, $slug)
    {
        $country = Country::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $query = Hotel::where('country_id', $country->id)->where('is_active', true);

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        if ($request->has('destination')) {
            $query->where('destination', 'like', '%' . $request->destination . '%');
        }
        if ($request->has('stars')) {
            $query->where('stars', $request->stars);
        }
        if ($request->has('min_price')) {
            $query->where('price_per_night', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price_per_night', '<=', $request->max_price);
        }

        $sort = $request->get('sort', 'rating');
        match ($sort) {
            'price_asc'  => $query->orderBy('price_per_night', 'asc'),
            'price_desc' => $query->orderBy('price_per_night', 'desc'),
            'stars'      => $query->orderBy('stars', 'desc'),
            default      => $query->orderBy('rating', 'desc'),
        };

        return response()->json($query->get());
    }

    /**
     * GET /api/hotels/{slug}
     * Return full hotel detail.
     */
    public function show($slug)
    {
        $hotel = Hotel::where('slug', $slug)
            ->where('is_active', true)
            ->with('country:id,name,slug')
            ->firstOrFail();

        return response()->json($hotel);
    }

    // ─── Admin Routes ──────────────────────────────────────────────────────────

    /** GET /api/admin/hotels */
    public function index()
    {
        return response()->json(
            Hotel::with('country:id,name,slug')->orderBy('title')->get()
        );
    }

    /** POST /api/admin/hotels */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id'      => 'required|exists:countries,id',
            'slug'            => 'required|string|max:150|unique:hotels',
            'title'           => 'required|string|max:255',
            'location'        => 'nullable|string|max:255',
            'image'           => 'nullable|string',
            'category'        => 'nullable|string|max:50',
            'stars'           => 'nullable|integer|min:1|max:5',
            'destination'     => 'nullable|string|max:100',
            'badge'           => 'nullable|string|max:50',
            'rating'          => 'nullable|numeric|min:0|max:5',
            'reviews_count'   => 'nullable|integer',
            'price_per_night' => 'required|numeric|min:0',
            'currency'        => 'nullable|string|max:10',
            'amenities'       => 'nullable|array',
            'description'     => 'nullable|string',
            'policies'        => 'nullable|string',
            'is_active'       => 'boolean',
        ]);

        $hotel = Hotel::create($validated);
        return response()->json($hotel, 201);
    }

    /** PUT /api/admin/hotels/{id} */
    public function update(Request $request, $id)
    {
        $hotel = Hotel::findOrFail($id);

        $validated = $request->validate([
            'title'           => 'sometimes|string|max:255',
            'location'        => 'nullable|string|max:255',
            'image'           => 'nullable|string',
            'category'        => 'nullable|string|max:50',
            'stars'           => 'nullable|integer|min:1|max:5',
            'destination'     => 'nullable|string|max:100',
            'badge'           => 'nullable|string|max:50',
            'rating'          => 'nullable|numeric|min:0|max:5',
            'reviews_count'   => 'nullable|integer',
            'price_per_night' => 'sometimes|numeric|min:0',
            'currency'        => 'nullable|string|max:10',
            'amenities'       => 'nullable|array',
            'description'     => 'nullable|string',
            'policies'        => 'nullable|string',
            'is_active'       => 'boolean',
        ]);

        $hotel->update($validated);
        return response()->json(['message' => 'Hotel updated.', 'hotel' => $hotel]);
    }

    /** DELETE /api/admin/hotels/{id} */
    public function destroy($id)
    {
        Hotel::findOrFail($id)->delete();
        return response()->json(['message' => 'Hotel deleted.']);
    }
}
