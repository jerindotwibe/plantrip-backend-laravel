<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * GET /api/user
     * Return the authenticated user's full profile.
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        $upcomingTrips = \App\Models\Booking::where('user_id', $user->id)
            ->whereDate('travel_date', '>=', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $pastTrips = \App\Models\Booking::where('user_id', $user->id)
            ->where(function ($q) {
                $q->whereDate('travel_date', '<', now())
                  ->orWhere('status', 'completed');
            })
            ->count();

        return response()->json([
            'id'                   => $user->id,
            'name'                 => $user->name,
            'email'                => $user->email,
            'phone'                => $user->phone,
            'country'              => $user->country,
            'role'                 => $user->email === 'admin@plantriponline.com' ? 'Admin' : 'User',
            'upcoming_trips_count' => $upcomingTrips,
            'past_trips_count'     => $pastTrips,
            'total_bookings_count' => \App\Models\Booking::where('user_id', $user->id)->count(),
            'created_at'           => $user->created_at,
        ]);
    }

    /**
     * PUT /api/user
     * Update the authenticated user's profile.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'    => 'sometimes|string|max:255',
            'phone'   => 'sometimes|string|max:50',
            'country' => 'sometimes|string|max:255',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user'    => [
                'id'      => $user->id,
                'name'    => $user->name,
                'email'   => $user->email,
                'phone'   => $user->phone,
                'country' => $user->country,
            ],
        ]);
    }

    // ─── Admin Routes ──────────────────────────────────────────────────────────

    /**
     * GET /api/admin/users
     * Return all users (admin only).
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get()->map(function ($user) {
            return [
                'id'      => $user->id,
                'name'    => $user->name,
                'email'   => $user->email,
                'phone'   => $user->phone,
                'country' => $user->country,
                'role'    => $user->email === 'admin@plantriponline.com' ? 'Admin' : 'User',
                'status'  => 'Active',
                'date'    => $user->created_at ? $user->created_at->format('Y-m-d') : 'N/A',
            ];
        });

        return response()->json(['users' => $users, 'total' => $users->count()]);
    }

    /**
     * GET /api/admin/users/{id}
     * Return a single user detail (admin only).
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'id'      => $user->id,
            'name'    => $user->name,
            'email'   => $user->email,
            'phone'   => $user->phone,
            'country' => $user->country,
            'role'    => $user->email === 'admin@plantriponline.com' ? 'Admin' : 'User',
            'status'  => 'Active',
            'date'    => $user->created_at ? $user->created_at->format('Y-m-d') : 'N/A',
        ]);
    }

    /**
     * PUT /api/admin/users/{id}
     * Update any user's info (admin only).
     */
    public function adminUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'    => 'sometimes|string|max:255',
            'phone'   => 'sometimes|string|max:50',
            'country' => 'sometimes|string|max:255',
        ]);

        $user->update($validated);

        return response()->json(['message' => 'User updated successfully.', 'user' => $user]);
    }

    /**
     * DELETE /api/admin/users/{id}
     * Delete a user (admin only, cannot delete self).
     */
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'You cannot delete your own admin account.'], 403);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }
}
