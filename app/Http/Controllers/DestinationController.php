<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    /**
     * GET /api/countries/{slug}/destinations
     * List all active destinations for a country.
     */
    public function byCountry($slug)
    {
        $country = Country::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $destinations = Destination::where('country_id', $country->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json($destinations);
    }

    /**
     * GET /api/destinations/{slug}
     * Return a single destination with its day tours.
     */
    public function show($slug)
    {
        $destination = Destination::where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'country:id,name,slug',
                'dayTours' => fn($q) => $q->where('is_active', true)->select('id', 'destination_id', 'slug', 'title', 'image', 'duration', 'price', 'currency', 'rating'),
            ])
            ->firstOrFail();

        return response()->json($destination);
    }

    // ─── Admin Routes ──────────────────────────────────────────────────────────

    /**
     * POST /api/admin/destinations
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id'      => 'required|exists:countries,id',
            'name'            => 'required|string|max:100',
            'slug'            => 'required|string|max:100|unique:destinations',
            'image'           => 'nullable|string|url',
            'badge'           => 'nullable|string|max:50',
            'rating'          => 'nullable|numeric|min:0|max:5',
            'locations_count' => 'nullable|integer',
            'description'     => 'nullable|string',
            'highlights'      => 'nullable|array',
            'is_active'       => 'boolean',
        ]);

        $destination = Destination::create($validated);
        return response()->json($destination, 201);
    }

    /**
     * PUT /api/admin/destinations/{id}
     */
    public function update(Request $request, $id)
    {
        $destination = Destination::findOrFail($id);

        $validated = $request->validate([
            'name'            => 'sometimes|string|max:100',
            'image'           => 'nullable|string|url',
            'badge'           => 'nullable|string|max:50',
            'rating'          => 'nullable|numeric|min:0|max:5',
            'locations_count' => 'nullable|integer',
            'description'     => 'nullable|string',
            'highlights'      => 'nullable|array',
            'is_active'       => 'boolean',
        ]);

        $destination->update($validated);
        return response()->json(['message' => 'Destination updated.', 'destination' => $destination]);
    }

    /**
     * DELETE /api/admin/destinations/{id}
     */
    public function destroy($id)
    {
        Destination::findOrFail($id)->delete();
        return response()->json(['message' => 'Destination deleted.']);
    }
}
