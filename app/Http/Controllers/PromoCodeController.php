<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    /**
     * POST /api/promo-codes/validate
     * Validate a promo code and return the discount amount.
     */
    public function validate(Request $request)
    {
        $request->validate([
            'code'         => 'required|string|max:50',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $promo = PromoCode::where('code', strtoupper($request->code))->first();

        if (!$promo || !$promo->isValid()) {
            return response()->json([
                'valid'   => false,
                'message' => 'Invalid or expired promo code.',
            ], 422);
        }

        $discount = $promo->calculateDiscount((float) $request->total_amount);

        if ($discount === 0.0 && $request->total_amount < $promo->min_amount) {
            return response()->json([
                'valid'   => false,
                'message' => "This code requires a minimum order of \${$promo->min_amount}.",
            ], 422);
        }

        return response()->json([
            'valid'          => true,
            'code'           => $promo->code,
            'discount_type'  => $promo->discount_type,
            'discount_value' => $promo->discount_value,
            'discount_amount'=> $discount,
            'message'        => "Promo code applied! You save \${$discount}.",
        ]);
    }

    // ─── Admin Routes ──────────────────────────────────────────────────────────

    /** GET /api/admin/promo-codes */
    public function index()
    {
        return response()->json(PromoCode::orderBy('created_at', 'desc')->get());
    }

    /** POST /api/admin/promo-codes */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'           => 'required|string|max:50|unique:promo_codes',
            'discount_type'  => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric|min:0',
            'min_amount'     => 'nullable|numeric|min:0',
            'max_uses'       => 'nullable|integer|min:1',
            'expires_at'     => 'nullable|date|after:today',
            'is_active'      => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $promo = PromoCode::create($validated);
        return response()->json($promo, 201);
    }

    /** PUT /api/admin/promo-codes/{id} */
    public function update(Request $request, $id)
    {
        $promo = PromoCode::findOrFail($id);

        $validated = $request->validate([
            'discount_type'  => 'sometimes|in:fixed,percentage',
            'discount_value' => 'sometimes|numeric|min:0',
            'min_amount'     => 'nullable|numeric|min:0',
            'max_uses'       => 'nullable|integer|min:1',
            'expires_at'     => 'nullable|date',
            'is_active'      => 'boolean',
        ]);

        $promo->update($validated);
        return response()->json(['message' => 'Promo code updated.', 'promo' => $promo]);
    }

    /** DELETE /api/admin/promo-codes/{id} */
    public function destroy($id)
    {
        PromoCode::findOrFail($id)->delete();
        return response()->json(['message' => 'Promo code deleted.']);
    }
}
