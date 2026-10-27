<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * POST /api/bookings
     * Create a new booking (public — guests can also book).
     */
    public function store(Request $request)
    {
        // Normalize camelCase / alias inputs from frontend if present
        $mergeData = [];
        if ($request->filled('firstName') && !$request->filled('first_name')) {
            $mergeData['first_name'] = $request->input('firstName');
        }
        if ($request->filled('lastName') && !$request->filled('last_name')) {
            $mergeData['last_name'] = $request->input('lastName');
        }
        if ($request->filled('date') && !$request->filled('travel_date')) {
            $mergeData['travel_date'] = $request->input('date');
        }
        if ($request->filled('travelDate') && !$request->filled('travel_date')) {
            $mergeData['travel_date'] = $request->input('travelDate');
        }
        if ($request->filled('telephone') && !$request->filled('phone')) {
            $mergeData['phone'] = $request->input('telephone');
        }
        if ($request->filled('countryCode') && !$request->filled('country_code')) {
            $mergeData['country_code'] = $request->input('countryCode');
        }
        if ($request->filled('customPickup') && (!$request->filled('pick_up') || $request->input('pick_up') === 'custom')) {
            $mergeData['pick_up'] = $request->input('customPickup');
        } elseif ($request->filled('pickUp') && !$request->filled('pick_up')) {
            $mergeData['pick_up'] = $request->input('pickUp');
        }
        if ($request->filled('customDrop') && (!$request->filled('drop_point') || $request->input('drop_point') === 'custom')) {
            $mergeData['drop_point'] = $request->input('customDrop');
        } elseif ($request->filled('dropPoint') && !$request->filled('drop_point')) {
            $mergeData['drop_point'] = $request->input('dropPoint');
        }
        if ($request->filled('promoCode') && !$request->filled('promo_code')) {
            $mergeData['promo_code'] = $request->input('promoCode');
        }
        if ($request->filled('paymentMethod') && !$request->filled('payment_method')) {
            $mergeData['payment_method'] = $request->input('paymentMethod');
        }
        if ($request->filled('specialRequests') && !$request->filled('special_requests')) {
            $mergeData['special_requests'] = $request->input('specialRequests');
        }
        if ($request->has('kids') && is_array($request->input('kids')) && !$request->filled('children')) {
            $mergeData['children'] = count($request->input('kids'));
        } elseif ($request->has('children') && is_array($request->input('children'))) {
            $mergeData['children'] = count($request->input('children'));
        }
        if ($request->filled('total') && !$request->filled('total_amount')) {
            $mergeData['total_amount'] = $request->input('total');
        }
        if ($request->filled('totalAmount') && !$request->filled('total_amount')) {
            $mergeData['total_amount'] = $request->input('totalAmount');
        }
        if (!$request->filled('subtotal') && ($request->filled('total_amount') || isset($mergeData['total_amount']))) {
            $mergeData['subtotal'] = $request->filled('total_amount') ? $request->input('total_amount') : $mergeData['total_amount'];
        }

        if (!empty($mergeData)) {
            $request->merge($mergeData);
        }

        $validated = $request->validate([
            // Trip info
            'destination'     => 'required|string|max:255',
            'country'         => 'nullable|string|max:100',
            'tour_id'         => 'nullable|exists:day_tours,id',
            'package_id'      => 'nullable|exists:tour_packages,id',

            // Traveller
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'email'           => 'required|email|max:255',
            'phone'           => 'nullable|string|max:50',
            'country_code'    => 'nullable|string|max:10',
            'whatsapp'        => 'nullable|string|max:50',
            'nationality'     => 'nullable|string|max:100',

            // Trip details
            'travel_date'     => 'required|date|after_or_equal:today',
            'adults'          => 'required|integer|min:1',
            'children'        => 'nullable|integer|min:0',
            'pick_up'         => 'nullable|string|max:255',
            'drop_point'      => 'nullable|string|max:255',
            'special_requests'=> 'nullable|string|max:1000',

            // Payment
            'payment_method'  => 'required|in:online,offline',
            'promo_code'      => 'nullable|string|max:50',
            'subtotal'        => 'required|numeric|min:0',
            'discount'        => 'nullable|numeric|min:0',
            'taxes'           => 'nullable|numeric|min:0',
            'total_amount'    => 'required|numeric|min:0',
            'currency'        => 'nullable|string|max:10',
        ]);

        // If promo code provided, increment its usage counter
        if (!empty($validated['promo_code'])) {
            $promo = PromoCode::where('code', strtoupper($validated['promo_code']))->first();
            if ($promo && $promo->isValid()) {
                $promo->increment('uses_count');
            }
        }

        // Attach authenticated user if logged in
        $validated['user_id'] = Auth::guard('sanctum')->id();
        $validated['booking_reference'] = Booking::generateReference();

        // Set initial statuses
        $validated['status']         = 'confirmed';
        $validated['payment_status'] = $validated['payment_method'] === 'offline' ? 'unpaid' : 'unpaid';

        $booking = Booking::create($validated);

        return response()->json([
            'message'          => 'Booking created successfully!',
            'booking_id'       => $booking->id,
            'booking_reference'=> $booking->booking_reference,
            'status'           => $booking->status,
            'payment_status'   => $booking->payment_status,
            'total_amount'     => $booking->total_amount,
            'currency'         => $booking->currency ?? 'USD',
        ], 201);
    }

    /**
     * GET /api/bookings
     * Return the authenticated user's own bookings.
     */
    public function myBookings(Request $request)
    {
        $bookings = Booking::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($bookings);
    }

    /**
     * GET /api/bookings/{id}
     * Return a single booking (must belong to the authenticated user).
     */
    public function show(Request $request, $id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with(['tour:id,title,slug', 'package:id,title,slug'])
            ->firstOrFail();

        return response()->json($booking);
    }

    // ─── Admin Routes ──────────────────────────────────────────────────────────

    /**
     * GET /api/admin/bookings
     * List all bookings with optional filters and pagination.
     */
    public function adminIndex(Request $request)
    {
        $query = Booking::with(['user:id,name,email'])
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $perPage  = $request->get('per_page', 15);
        $bookings = $query->paginate($perPage);

        // Revenue stats
        $stats = [
            'total_bookings'  => Booking::count(),
            'active_bookings' => Booking::whereIn('status', ['pending', 'confirmed'])->count(),
            'total_revenue'   => Booking::where('payment_status', 'paid')->sum('total_amount'),
            'pending_count'   => Booking::where('status', 'pending')->count(),
        ];

        return response()->json([
            'bookings' => $bookings,
            'stats'    => $stats,
        ]);
    }

    /**
     * GET /api/admin/bookings/{id}
     * Return a single booking detail (admin).
     */
    public function adminShow($id)
    {
        $booking = Booking::with([
            'user:id,name,email',
            'tour:id,title,slug',
            'package:id,title,slug',
        ])->findOrFail($id);

        return response()->json($booking);
    }

    /**
     * PUT /api/admin/bookings/{id}
     * Update booking status / payment status / notes.
     */
    public function adminUpdate(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $validated = $request->validate([
            'status'         => 'sometimes|in:pending,confirmed,cancelled,completed',
            'payment_status' => 'sometimes|in:unpaid,paid,refunded',
            'admin_notes'    => 'nullable|string|max:2000',
        ]);

        $booking->update($validated);

        return response()->json([
            'message' => 'Booking updated successfully.',
            'booking' => $booking,
        ]);
    }

    /**
     * DELETE /api/admin/bookings/{id}
     * Delete a booking (admin).
     */
    public function adminDestroy($id)
    {
        Booking::findOrFail($id)->delete();
        return response()->json(['message' => 'Booking deleted.']);
    }
}
