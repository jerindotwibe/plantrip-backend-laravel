<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * GET /api/reviews
     * List active reviews with optional filters.
     */
    public function index(Request $request)
    {
        $query = Review::where('is_active', true);

        if ($request->boolean('featured') || $request->input('featured') === '1') {
            $query->where('is_featured', true);
        }

        if ($request->filled('trip')) {
            $query->where('trip', 'like', "%{$request->trip}%");
        }

        if ($request->filled('rating')) {
            $query->where('rating', '>=', (int) $request->rating);
        }

        $limit = $request->get('limit', null);
        $reviews = $limit ? $query->latest()->limit((int) $limit)->get() : $query->latest()->get();

        return response()->json($reviews);
    }

    /**
     * GET /api/reviews/{id}
     */
    public function show($id)
    {
        $review = Review::where('is_active', true)->findOrFail($id);
        return response()->json($review);
    }

    // ─── Admin Endpoints ───────────────────────────────────────────────────────

    /**
     * GET /api/admin/reviews
     */
    public function adminIndex()
    {
        return response()->json(Review::orderBy('created_at', 'desc')->get());
    }

    /**
     * POST /api/admin/reviews
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'trip'        => 'nullable|string|max:150',
            'rating'      => 'required|integer|min:1|max:5',
            'comment'     => 'required|string|max:2000',
            'date'        => 'nullable|string|max:50',
            'avatar'      => 'nullable|string',
            'is_featured' => 'boolean',
            'is_active'   => 'boolean',
        ]);

        $review = Review::create($validated);
        return response()->json($review, 201);
    }

    /**
     * PUT /api/admin/reviews/{id}
     */
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'sometimes|string|max:100',
            'trip'        => 'nullable|string|max:150',
            'rating'      => 'sometimes|integer|min:1|max:5',
            'comment'     => 'sometimes|string|max:2000',
            'date'        => 'nullable|string|max:50',
            'avatar'      => 'nullable|string',
            'is_featured' => 'boolean',
            'is_active'   => 'boolean',
        ]);

        $review->update($validated);
        return response()->json(['message' => 'Review updated.', 'review' => $review]);
    }

    /**
     * DELETE /api/admin/reviews/{id}
     */
    public function destroy($id)
    {
        Review::findOrFail($id)->delete();
        return response()->json(['message' => 'Review deleted.']);
    }
}

