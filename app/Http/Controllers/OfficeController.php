<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    /**
     * GET /api/offices
     * List all active offices in presentation sort order.
     */
    public function index()
    {
        $offices = Office::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json($offices);
    }

    /**
     * GET /api/offices/{id}
     */
    public function show($id)
    {
        $office = Office::where('is_active', true)->findOrFail($id);
        return response()->json($office);
    }

    // ─── Admin Endpoints ───────────────────────────────────────────────────────

    /**
     * GET /api/admin/offices
     */
    public function adminIndex()
    {
        return response()->json(Office::orderBy('sort_order')->orderBy('id')->get());
    }

    /**
     * POST /api/admin/offices
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:150',
            'address'       => 'required|string|max:255',
            'phone'         => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:100',
            'map_embed_url' => 'nullable|string',
            'map_image_url' => 'nullable|string',
            'sort_order'    => 'nullable|integer',
            'is_active'     => 'boolean',
        ]);

        $office = Office::create($validated);
        return response()->json($office, 201);
    }

    /**
     * PUT /api/admin/offices/{id}
     */
    public function update(Request $request, $id)
    {
        $office = Office::findOrFail($id);

        $validated = $request->validate([
            'title'         => 'sometimes|string|max:150',
            'address'       => 'sometimes|string|max:255',
            'phone'         => 'nullable|string|max:50',
            'email'         => 'nullable|email|max:100',
            'map_embed_url' => 'nullable|string',
            'map_image_url' => 'nullable|string',
            'sort_order'    => 'nullable|integer',
            'is_active'     => 'boolean',
        ]);

        $office->update($validated);
        return response()->json(['message' => 'Office updated.', 'office' => $office]);
    }

    /**
     * DELETE /api/admin/offices/{id}
     */
    public function destroy($id)
    {
        Office::findOrFail($id)->delete();
        return response()->json(['message' => 'Office deleted.']);
    }
}

