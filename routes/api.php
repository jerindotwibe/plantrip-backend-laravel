<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\DayTourController;
use App\Http\Controllers\TourPackageController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\DashboardController;

// ═══════════════════════════════════════════════════════════════════════════════
// PUBLIC ROUTES  (no auth required)
// ═══════════════════════════════════════════════════════════════════════════════

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::post('/register',        [AuthController::class, 'register']);
Route::post('/login',           [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password',  [AuthController::class, 'resetPassword']);

// ── Countries ─────────────────────────────────────────────────────────────────
Route::get('/countries',        [CountryController::class, 'index']);
Route::get('/countries/{slug}', [CountryController::class, 'show']);

// ── Destinations ──────────────────────────────────────────────────────────────
Route::get('/countries/{slug}/destinations', [DestinationController::class, 'byCountry']);
Route::get('/destinations/{slug}',           [DestinationController::class, 'show']);

// ── Day Tours ─────────────────────────────────────────────────────────────────
Route::get('/countries/{slug}/day-tours', [DayTourController::class, 'byCountry']);
Route::get('/tours/{slug}',               [DayTourController::class, 'show']);

// ── Tour Packages ─────────────────────────────────────────────────────────────
Route::get('/countries/{slug}/tour-packages', [TourPackageController::class, 'byCountry']);
Route::get('/packages/{slug}',                [TourPackageController::class, 'show']);

// ── Hotels ────────────────────────────────────────────────────────────────────
Route::get('/countries/{slug}/hotels', [HotelController::class, 'byCountry']);
Route::get('/hotels/{slug}',           [HotelController::class, 'show']);

// ── Transfers ─────────────────────────────────────────────────────────────────
Route::get('/countries/{slug}/transfers', [TransferController::class, 'byCountry']);
Route::get('/transfers/{slug}',           [TransferController::class, 'show']);

// ── Enquiry (public submission) ───────────────────────────────────────────────
Route::post('/enquiries', [EnquiryController::class, 'store']);

// ── Promo Code validation (public) ────────────────────────────────────────────
Route::post('/promo-codes/validate', [PromoCodeController::class, 'validate']);

// ── Bookings (public — guests can also book) ──────────────────────────────────
Route::post('/bookings', [BookingController::class, 'store']);


// ═══════════════════════════════════════════════════════════════════════════════
// AUTHENTICATED USER ROUTES  (Sanctum token required)
// ═══════════════════════════════════════════════════════════════════════════════

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Profile
    Route::get('/user',  [UserController::class, 'profile']);
    Route::put('/user',  [UserController::class, 'update']);

    // My Bookings
    Route::get('/bookings',     [BookingController::class, 'myBookings']);
    Route::get('/bookings/{id}',[BookingController::class, 'show']);
});


// ═══════════════════════════════════════════════════════════════════════════════
// ADMIN-ONLY ROUTES  (Sanctum token + admin email required)
// ═══════════════════════════════════════════════════════════════════════════════

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {

    // ── Dashboard ─────────────────────────────────────────────────────────────
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // ── Users ─────────────────────────────────────────────────────────────────
    Route::get('/users',        [UserController::class, 'index']);
    Route::get('/users/{id}',   [UserController::class, 'show']);
    Route::put('/users/{id}',   [UserController::class, 'adminUpdate']);
    Route::delete('/users/{id}',[UserController::class, 'destroy']);

    // ── Countries ─────────────────────────────────────────────────────────────
    Route::get('/countries',          [CountryController::class, 'adminIndex']);
    Route::post('/countries',         [CountryController::class, 'store']);
    Route::put('/countries/{id}',     [CountryController::class, 'update']);
    Route::delete('/countries/{id}',  [CountryController::class, 'destroy']);

    // ── Destinations ──────────────────────────────────────────────────────────
    Route::post('/destinations',         [DestinationController::class, 'store']);
    Route::put('/destinations/{id}',     [DestinationController::class, 'update']);
    Route::delete('/destinations/{id}',  [DestinationController::class, 'destroy']);

    // ── Day Tours ─────────────────────────────────────────────────────────────
    Route::get('/day-tours',          [DayTourController::class, 'index']);
    Route::post('/day-tours',         [DayTourController::class, 'store']);
    Route::put('/day-tours/{id}',     [DayTourController::class, 'update']);
    Route::delete('/day-tours/{id}',  [DayTourController::class, 'destroy']);

    // ── Tour Packages ─────────────────────────────────────────────────────────
    Route::get('/tour-packages',          [TourPackageController::class, 'index']);
    Route::post('/tour-packages',         [TourPackageController::class, 'store']);
    Route::put('/tour-packages/{id}',     [TourPackageController::class, 'update']);
    Route::delete('/tour-packages/{id}',  [TourPackageController::class, 'destroy']);

    // ── Hotels ────────────────────────────────────────────────────────────────
    Route::get('/hotels',          [HotelController::class, 'index']);
    Route::post('/hotels',         [HotelController::class, 'store']);
    Route::put('/hotels/{id}',     [HotelController::class, 'update']);
    Route::delete('/hotels/{id}',  [HotelController::class, 'destroy']);

    // ── Transfers ─────────────────────────────────────────────────────────────
    Route::get('/transfers',          [TransferController::class, 'index']);
    Route::post('/transfers',         [TransferController::class, 'store']);
    Route::put('/transfers/{id}',     [TransferController::class, 'update']);
    Route::delete('/transfers/{id}',  [TransferController::class, 'destroy']);

    // ── Enquiries ─────────────────────────────────────────────────────────────
    Route::get('/enquiries',          [EnquiryController::class, 'index']);
    Route::get('/enquiries/{id}',     [EnquiryController::class, 'show']);
    Route::put('/enquiries/{id}',     [EnquiryController::class, 'update']);
    Route::delete('/enquiries/{id}',  [EnquiryController::class, 'destroy']);

    // ── Bookings ──────────────────────────────────────────────────────────────
    Route::get('/bookings',           [BookingController::class, 'adminIndex']);
    Route::get('/bookings/{id}',      [BookingController::class, 'adminShow']);
    Route::put('/bookings/{id}',      [BookingController::class, 'adminUpdate']);
    Route::delete('/bookings/{id}',   [BookingController::class, 'adminDestroy']);

    // ── Promo Codes ───────────────────────────────────────────────────────────
    Route::get('/promo-codes',          [PromoCodeController::class, 'index']);
    Route::post('/promo-codes',         [PromoCodeController::class, 'store']);
    Route::put('/promo-codes/{id}',     [PromoCodeController::class, 'update']);
    Route::delete('/promo-codes/{id}',  [PromoCodeController::class, 'destroy']);
});
