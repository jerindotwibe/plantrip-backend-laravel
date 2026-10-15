<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * GET /api/countries
     * List all active countries.
     */
    public function index()
    {
        $countries = Country::where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json($countries);
    }

    /**
     * GET /api/countries/{slug}
     * Return full country data including day tours, destinations, hotels, packages, transfers.
     */
    public function show($slug)
    {
        $country = Country::where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'destinations' => fn($q) => $q->where('is_active', true)->orderBy('name'),
                'dayTours'     => fn($q) => $q->where('is_active', true)->orderBy('title'),
                'tourPackages' => fn($q) => $q->where('is_active', true)->orderBy('title'),
                'hotels'       => fn($q) => $q->where('is_active', true)->orderBy('title'),
                'transfers'    => fn($q) => $q->where('is_active', true)->orderBy('title'),
            ])
            ->firstOrFail();

        return response()->json($country);
    }

    // ─── Admin Routes ──────────────────────────────────────────────────────────

    /**
     * GET /api/admin/countries
     * List all countries including inactive (admin).
     */
    public function adminIndex()
    {
        return response()->json(Country::orderBy('name')->get());
    }

    /**
     * POST /api/admin/countries
     * Create a new country (admin).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'slug'          => 'required|string|max:100|unique:countries',
            'flag'          => 'nullable|string|max:10',
            'currency'      => 'nullable|string|max:10',
            'hero_image'    => 'nullable|string|url',
            'hero_title'    => 'nullable|string|max:255',
            'tagline'       => 'nullable|string|max:255',
            'description'   => 'nullable|string',
            'is_active'     => 'boolean',
        ]);

        $country = Country::create($validated);
        return response()->json($country, 201);
    }

    /**
     * PUT /api/admin/countries/{id}
     * Update a country (admin).
     */
    public function update(Request $request, $id)
    {
        $country = Country::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'sometimes|string|max:100',
            'flag'        => 'nullable|string|max:10',
            'currency'    => 'nullable|string|max:10',
            'hero_image'  => 'nullable|string|url',
            'hero_title'  => 'nullable|string|max:255',
            'tagline'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $country->update($validated);
        return response()->json(['message' => 'Country updated successfully.', 'country' => $country]);
    }

    /**
     * DELETE /api/admin/countries/{id}
     * Delete a country (admin).
     */
    public function destroy($id)
    {
        $country = Country::findOrFail($id);
        $country->delete();
        return response()->json(['message' => 'Country deleted successfully.']);
    }
}
