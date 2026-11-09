<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    /**
     * GET /api/hotels
     * List all hotels across countries with optional filters.
     */
    public function index(Request $request)
    {
        $query = Hotel::where('is_active', true)
            ->with(['country:id,name,slug,currency', 'destination:id,name,slug']);

        if ($request->filled('country')) {
            $query->whereHas('country', function ($q) use ($request) {
                $q->where('slug', $request->country)
                  ->orWhere('name', 'like', "%{$request->country}%");
            });
        }

        if ($request->filled('destination_id')) {
            $query->where('destination_id', $request->destination_id);
        }

        if ($request->boolean('featured') || $request->input('featured') === '1') {
            $query->where('is_featured', true);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('destination')) {
            $query->where('destination', 'like', '%' . $request->destination . '%');
        }
        if ($request->filled('stars')) {
            $query->where('stars', $request->stars);
        }
        if ($request->filled('min_price')) {
            $query->where('price_per_night', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price_per_night', '<=', $request->max_price);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%");
            });
        }

        $sort = $request->get('sort', 'rating');
        match ($sort) {
            'price_asc'  => $query->orderBy('price_per_night', 'asc'),
            'price_desc' => $query->orderBy('price_per_night', 'desc'),
            'stars'      => $query->orderBy('stars', 'desc'),
            default      => $query->orderBy('rating', 'desc'),
        };

        $limit = $request->get('limit', null);
        $hotels = $limit ? $query->limit((int) $limit)->get() : $query->get();

        return response()->json($hotels);
    }

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
            ->with(['country:id,name,slug', 'destination:id,name,slug'])
            ->firstOrFail();

        return response()->json($hotel);
    }

    // ─── Admin Routes ──────────────────────────────────────────────────────────

    /** GET /api/admin/hotels */
    public function adminIndex()
    {
        return response()->json(
            Hotel::with(['country:id,name,slug', 'destination:id,name,slug'])->orderBy('title')->get()
        );
    }

    /** POST /api/admin/hotels */
    public function store(Request $request)
    {
        // Support frontend sending 'price' instead of 'price_per_night'
        if ($request->has('price') && !$request->has('price_per_night')) {
            $request->merge(['price_per_night' => $request->price]);
        }

        $validated = $request->validate([
            'country_id'      => 'required|exists:countries,id',
            'destination_id'  => 'nullable|exists:destinations,id',
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
            'is_featured'     => 'boolean',
        ]);

        $hotel = Hotel::create($validated);
        return response()->json($hotel, 201);
    }

    /** PUT /api/admin/hotels/{id} */
    public function update(Request $request, $id)
    {
        $hotel = Hotel::findOrFail($id);

        // Support frontend sending 'price' instead of 'price_per_night'
        if ($request->has('price') && !$request->has('price_per_night')) {
            $request->merge(['price_per_night' => $request->price]);
        }

        $validated = $request->validate([
            'country_id'      => 'sometimes|exists:countries,id',
            'destination_id'  => 'nullable|exists:destinations,id',
            'slug'            => 'sometimes|string|max:150|unique:hotels,slug,' . $hotel->id,
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
            'is_featured'     => 'boolean',
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
