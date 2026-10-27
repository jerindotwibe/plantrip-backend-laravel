<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\TourPackage;
use Illuminate\Http\Request;

class TourPackageController extends Controller
{
    /**
     * GET /api/tour-packages or GET /api/packages
     * List all tour packages across countries with optional filters.
     */
    public function index(Request $request)
    {
        $query = TourPackage::where('is_active', true)
            ->with(['country:id,name,slug,currency']);

        if ($request->filled('country')) {
            $query->whereHas('country', function ($q) use ($request) {
                $q->where('slug', $request->country)
                  ->orWhere('name', 'like', "%{$request->country}%");
            });
        }

        if ($request->boolean('featured') || $request->input('featured') === '1') {
            $query->where('is_featured', true);
        }

        if ($request->filled('duration')) {
            $query->where('duration_days', $request->duration);
        }
        if ($request->filled('type')) {
            $query->where('badge', 'like', '%' . $request->type . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('subtitle', 'like', "%{$search}%");
            });
        }

        $sort = $request->get('sort', 'popular');
        match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default      => $query->orderBy('title', 'asc'),
        };

        $limit = $request->get('limit', null);
        $packages = $limit ? $query->limit((int) $limit)->get() : $query->get();

        return response()->json($packages);
    }

    /**
     * GET /api/countries/{slug}/tour-packages
     * List tour packages for a country with optional filters.
     */
    public function byCountry(Request $request, $slug)
    {
        $country = Country::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $query = TourPackage::where('country_id', $country->id)->where('is_active', true);

        if ($request->has('duration')) {
            $query->where('duration_days', $request->duration);
        }
        if ($request->has('type')) {
            $query->where('badge', 'like', '%' . $request->type . '%');
        }

        $sort = $request->get('sort', 'popular');
        match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default      => $query->orderBy('title', 'asc'),
        };

        return response()->json($query->get());
    }

    /**
     * GET /api/packages/{slug}
     * Return full package detail.
     */
    public function show($slug)
    {
        $package = TourPackage::where('slug', $slug)
            ->where('is_active', true)
            ->with('country:id,name,slug')
            ->firstOrFail();

        return response()->json($package);
    }

    // ─── Admin Routes ──────────────────────────────────────────────────────────

    /** GET /api/admin/tour-packages */
    public function adminIndex()
    {
        return response()->json(
            TourPackage::with('country:id,name,slug')->orderBy('title')->get()
        );
    }

    /** POST /api/admin/tour-packages */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id'      => 'required|exists:countries,id',
            'slug'            => 'required|string|max:150|unique:tour_packages',
            'title'           => 'required|string|max:255',
            'subtitle'        => 'nullable|string|max:255',
            'image'           => 'nullable|string',
            'badge'           => 'nullable|string|max:50',
            'duration'        => 'nullable|string|max:50',
            'duration_days'   => 'nullable|integer|min:1',
            'duration_nights' => 'nullable|integer|min:0',
            'price'           => 'required|numeric|min:0',
            'currency'        => 'nullable|string|max:10',
            'includes'        => 'nullable|array',
            'itinerary'       => 'nullable|array',
            'highlights'      => 'nullable|array',
            'terms'           => 'nullable|string',
            'is_active'       => 'boolean',
            'is_featured'     => 'boolean',
        ]);

        $package = TourPackage::create($validated);
        return response()->json($package, 201);
    }

    /** PUT /api/admin/tour-packages/{id} */
    public function update(Request $request, $id)
    {
        $package = TourPackage::findOrFail($id);

        $validated = $request->validate([
            'title'           => 'sometimes|string|max:255',
            'subtitle'        => 'nullable|string|max:255',
            'image'           => 'nullable|string',
            'badge'           => 'nullable|string|max:50',
            'duration'        => 'nullable|string|max:50',
            'duration_days'   => 'nullable|integer|min:1',
            'duration_nights' => 'nullable|integer|min:0',
            'price'           => 'sometimes|numeric|min:0',
            'currency'        => 'nullable|string|max:10',
            'includes'        => 'nullable|array',
            'itinerary'       => 'nullable|array',
            'highlights'      => 'nullable|array',
            'terms'           => 'nullable|string',
            'is_active'       => 'boolean',
            'is_featured'     => 'boolean',
        ]);

        $package->update($validated);
        return response()->json(['message' => 'Tour package updated.', 'package' => $package]);
    }

    /** DELETE /api/admin/tour-packages/{id} */
    public function destroy($id)
    {
        TourPackage::findOrFail($id)->delete();
        return response()->json(['message' => 'Tour package deleted.']);
    }
}
