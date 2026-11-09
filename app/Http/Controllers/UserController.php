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
    public function index(Request $request)
    {
        $query = User::orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('role') && $request->role !== 'All') {
            $query->where('role', strtolower($request->role));
        }

        $users = $query->get()->map(function ($user) {
            $role = $user->role ?: ($user->email === 'admin@plantriponline.com' ? 'Admin' : 'User');
            $status = $user->status ?: 'Active';

            return [
                'id'      => $user->id,
                'name'    => $user->name,
                'email'   => $user->email,
                'phone'   => $user->phone,
                'country' => $user->country,
                'role'    => ucfirst($role),
                'status'  => ucfirst($status),
                'date'    => $user->created_at ? $user->created_at->format('Y-m-d') : 'N/A',
                'created_at' => $user->created_at,
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
        $role = $user->role ?: ($user->email === 'admin@plantriponline.com' ? 'Admin' : 'User');
        $status = $user->status ?: 'Active';

        return response()->json([
            'id'      => $user->id,
            'name'    => $user->name,
            'email'   => $user->email,
            'phone'   => $user->phone,
            'country' => $user->country,
            'role'    => ucfirst($role),
            'status'  => ucfirst($status),
            'date'    => $user->created_at ? $user->created_at->format('Y-m-d') : 'N/A',
            'created_at' => $user->created_at,
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
            'phone'   => 'sometimes|nullable|string|max:50',
            'country' => 'sometimes|nullable|string|max:255',
            'role'    => 'sometimes|string|max:50',
            'status'  => 'sometimes|string|max:50',
        ]);

        if (isset($validated['role'])) {
            $validated['role'] = strtolower($validated['role']);
        }
        if (isset($validated['status'])) {
            $validated['status'] = strtolower($validated['status']);
        }

        $user->update($validated);

        return response()->json(['message' => 'User updated successfully.', 'user' => $user]);
    }

    /**
     * PUT /api/admin/profile
     * Update admin user profile name.
     */
    public function updateAdminProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user'    => $user,
        ]);
    }

    /**
     * PUT /api/admin/profile/password
     * Update admin user password.
     */
    public function updateAdminPassword(Request $request)
    {
        $user = $request->user();

        // Support either 'password' or 'new_password' as sent by frontend
        if ($request->has('password') && !$request->has('new_password')) {
            $request->merge(['new_password' => $request->password]);
        }
        if ($request->has('password_confirmation') && !$request->has('new_password_confirmation')) {
            $request->merge(['new_password_confirmation' => $request->password_confirmation]);
        }

        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'The provided current password does not match.'], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json(['message' => 'Password updated successfully.']);
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
