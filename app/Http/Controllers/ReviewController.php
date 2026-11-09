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
    public function adminIndex(Request $request)
    {
        $query = Review::orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', strtolower($request->status));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%")
                  ->orWhere('trip', 'like', "%{$search}%")
                  ->orWhere('comment', 'like', "%{$search}%");
            });
        }

        if ($request->filled('per_page')) {
            return response()->json($query->paginate((int) $request->per_page));
        }

        return response()->json($query->get());
    }

    /**
     * POST /api/admin/reviews
     */
    public function store(Request $request)
    {
        if ($request->filled('destination') && !$request->filled('trip')) {
            $request->merge(['trip' => $request->destination]);
        } elseif ($request->filled('trip') && !$request->filled('destination')) {
            $request->merge(['destination' => $request->trip]);
        }

        if ($request->filled('status')) {
            $status = strtolower($request->status);
            $request->merge(['status' => $status]);
            if ($status === 'approved' && !$request->has('is_active')) {
                $request->merge(['is_active' => true]);
            } elseif ($status === 'rejected' && !$request->has('is_active')) {
                $request->merge(['is_active' => false]);
            }
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'nullable|email|max:150',
            'trip'        => 'nullable|string|max:150',
            'destination' => 'nullable|string|max:150',
            'rating'      => 'required|integer|min:1|max:5',
            'comment'     => 'required|string|max:2000',
            'date'        => 'nullable|string|max:50',
            'avatar'      => 'nullable|string',
            'status'      => 'nullable|string|in:pending,approved,rejected',
            'admin_note'  => 'nullable|string|max:1000',
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

        if ($request->filled('destination') && !$request->filled('trip')) {
            $request->merge(['trip' => $request->destination]);
        } elseif ($request->filled('trip') && !$request->filled('destination')) {
            $request->merge(['destination' => $request->trip]);
        }

        if ($request->filled('status')) {
            $status = strtolower($request->status);
            $request->merge(['status' => $status]);
            if ($status === 'approved' && !$request->has('is_active')) {
                $request->merge(['is_active' => true]);
            } elseif ($status === 'rejected' && !$request->has('is_active')) {
                $request->merge(['is_active' => false]);
            }
        }

        $validated = $request->validate([
            'name'        => 'sometimes|string|max:100',
            'email'       => 'nullable|email|max:150',
            'trip'        => 'nullable|string|max:150',
            'destination' => 'nullable|string|max:150',
            'rating'      => 'sometimes|integer|min:1|max:5',
            'comment'     => 'sometimes|string|max:2000',
            'date'        => 'nullable|string|max:50',
            'avatar'      => 'nullable|string',
            'status'      => 'nullable|string|in:pending,approved,rejected',
            'admin_note'  => 'nullable|string|max:1000',
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

