<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * GET /api/settings
     * Return public application settings as a key-value dictionary.
     */
    public function index(Request $request)
    {
        $query = Setting::query();

        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        $settings = $query->get()->pluck('value', 'key');

        return response()->json($settings);
    }

    // ─── Admin Endpoints ───────────────────────────────────────────────────────

    /**
     * GET /api/admin/settings
     */
    public function adminIndex()
    {
        return response()->json(Setting::orderBy('group')->orderBy('key')->get());
    }

    /**
     * PUT /api/admin/settings
     * Bulk update key-value settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'group'    => 'nullable|string|max:50',
        ]);

        $group = $request->get('group', 'general');

        foreach ($validated['settings'] as $key => $value) {
            Setting::set($key, $value, $group);
        }

        return response()->json([
            'message'  => 'Settings updated successfully.',
            'settings' => Setting::all()->pluck('value', 'key'),
        ]);
    }
}

