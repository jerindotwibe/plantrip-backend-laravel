<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\DayTour;
use App\Models\Destination;
use App\Models\Review;
use App\Models\Setting;
use App\Models\TourPackage;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * GET /api/home
     * Unified homepage feed delivering countries, popular destinations,
     * trending day tours, tour packages, and testimonials in a single query.
     */
    public function index(Request $request)
    {
        // Active Countries
        $countries = Country::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'flag', 'currency', 'hero_image', 'hero_title', 'tagline', 'description', 'tour_count', 'package_count']);

        // Popular Destinations (featured or top rating)
        $destinationsQuery = Destination::where('is_active', true)
            ->with('country:id,name,slug,currency');

        $destinations = $destinationsQuery->where(function ($q) {
                $q->where('is_featured', true)->orWhere('rating', '>=', 4.7);
            })
            ->orderByDesc('is_featured')
            ->orderByDesc('rating')
            ->limit(8)
            ->get();

        // Trending Day Tours (featured or top reviews)
        $tours = DayTour::where('is_active', true)
            ->with(['country:id,name,slug,currency', 'destination:id,name,slug'])
            ->orderByDesc('is_featured')
            ->orderByDesc('rating')
            ->limit(6)
            ->get();

        // Featured Packages
        $packages = TourPackage::where('is_active', true)
            ->with('country:id,name,slug,currency')
            ->orderByDesc('is_featured')
            ->orderBy('price')
            ->limit(6)
            ->get();

        // Customer Reviews
        $reviews = Review::where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->limit(6)
            ->get();

        // Contact & Brand settings
        $settings = Setting::all()->pluck('value', 'key');

        return response()->json([
            'countries'             => $countries,
            'featured_destinations' => $destinations,
            'trending_tours'        => $tours,
            'featured_packages'     => $packages,
            'reviews'               => $reviews,
            'settings'              => $settings,
        ]);
    }
}

