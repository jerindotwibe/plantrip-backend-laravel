<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Transfer;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    /**
     * GET /api/countries/{slug}/transfers
     * List all transfers for a country.
     */
    public function byCountry($slug)
    {
        $country = Country::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $transfers = Transfer::where('country_id', $country->id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get();

        return response()->json($transfers);
    }

    /**
     * GET /api/transfers/{slug}
     * Return a single transfer's detail.
     */
    public function show($slug)
    {
        $transfer = Transfer::where('slug', $slug)
            ->where('is_active', true)
            ->with('country:id,name,slug')
            ->firstOrFail();

        return response()->json($transfer);
    }

    // ─── Admin Routes ──────────────────────────────────────────────────────────

    /** GET /api/admin/transfers */
    public function index()
    {
        return response()->json(
            Transfer::with('country:id,name,slug')->orderBy('title')->get()
        );
    }

    /** POST /api/admin/transfers */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id'    => 'required|exists:countries,id',
            'slug'          => 'required|string|max:150|unique:transfers',
            'title'         => 'required|string|max:255',
            'from_location' => 'required|string|max:255',
            'to_location'   => 'required|string|max:255',
            'duration'      => 'nullable|string|max:50',
            'price'         => 'required|numeric|min:0',
            'currency'      => 'nullable|string|max:10',
            'image'         => 'nullable|string',
            'vehicles'      => 'nullable|array',
            'description'   => 'nullable|string',
            'is_active'     => 'boolean',
        ]);

        $transfer = Transfer::create($validated);
        return response()->json($transfer, 201);
    }

    /** PUT /api/admin/transfers/{id} */
    public function update(Request $request, $id)
    {
        $transfer = Transfer::findOrFail($id);

        $validated = $request->validate([
            'title'         => 'sometimes|string|max:255',
            'from_location' => 'sometimes|string|max:255',
            'to_location'   => 'sometimes|string|max:255',
            'duration'      => 'nullable|string|max:50',
            'price'         => 'sometimes|numeric|min:0',
            'currency'      => 'nullable|string|max:10',
            'image'         => 'nullable|string',
            'vehicles'      => 'nullable|array',
            'description'   => 'nullable|string',
            'is_active'     => 'boolean',
        ]);

        $transfer->update($validated);
        return response()->json(['message' => 'Transfer updated.', 'transfer' => $transfer]);
    }

    /** DELETE /api/admin/transfers/{id} */
    public function destroy($id)
    {
        Transfer::findOrFail($id)->delete();
        return response()->json(['message' => 'Transfer deleted.']);
    }
}
