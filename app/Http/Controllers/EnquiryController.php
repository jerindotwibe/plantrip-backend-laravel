<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    /**
     * POST /api/enquiries
     * Submit a travel enquiry (public — no auth required).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'    => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'nullable|string|max:50',
            'country'      => 'nullable|string|max:100',
            'destination'  => 'nullable|string|max:100',
            'travel_type'  => 'nullable|string|max:50',
            'start_date'   => 'nullable|date|after_or_equal:today',
            'duration'     => 'nullable|string|max:50',
            'message'      => 'nullable|string|max:2000',
        ]);

        $enquiry = Enquiry::create($validated);

        return response()->json([
            'message'    => 'Your enquiry has been submitted successfully. We will contact you within 24 hours.',
            'enquiry_id' => $enquiry->id,
        ], 201);
    }

    // ─── Admin Routes ──────────────────────────────────────────────────────────

    /**
     * GET /api/admin/enquiries
     * List all enquiries with optional status filter and pagination.
     */
    public function index(Request $request)
    {
        $query = Enquiry::orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $enquiries = $query->paginate($perPage);

        return response()->json($enquiries);
    }

    /**
     * GET /api/admin/enquiries/{id}
     * Return a single enquiry detail.
     */
    public function show($id)
    {
        $enquiry = Enquiry::findOrFail($id);
        return response()->json($enquiry);
    }

    /**
     * PUT /api/admin/enquiries/{id}
     * Update enquiry status and admin notes.
     */
    public function update(Request $request, $id)
    {
        $enquiry = Enquiry::findOrFail($id);

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,in_progress,completed,cancelled',
            'notes'  => 'nullable|string|max:2000',
        ]);

        $enquiry->update($validated);

        return response()->json([
            'message'  => 'Enquiry updated successfully.',
            'enquiry'  => $enquiry,
        ]);
    }

    /**
     * DELETE /api/admin/enquiries/{id}
     * Delete an enquiry.
     */
    public function destroy($id)
    {
        Enquiry::findOrFail($id)->delete();
        return response()->json(['message' => 'Enquiry deleted.']);
    }
}
