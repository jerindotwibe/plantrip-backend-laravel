# Backend Database Gap Analysis Report

> **Project:** PlanTripOnline (Backend: `plantrip-laravel` | Frontend: `PlanTripOnline-Frontend`)  
> **Author:** Senior Laravel Backend Developer  
> **Date:** November 2026  
> **Status:** Completed Audit & Specification  

---

## 1. Executive Summary

A comprehensive, line-by-line audit comparing the updated `PlanTripOnline-Frontend` SPA with the `plantrip-laravel` backend database schemas, Eloquent models, migrations, controllers, and API routes was conducted.

### Key Metrics
- **Total Frontend Fields & Attributes Analysed:** 114 fields across 12 distinct functional modules and forms.
- **Confirmed Missing Columns Identified:** 11 columns across 5 existing tables (`users`, `countries`, `tour_packages`, `hotels`, `transfers`, `reviews`).
- **Confirmed Missing Tables:** 0 (all 13 core domain entities have corresponding tables in the database; no new tables are required).
- **Existing Columns Reused / Remapped:** 8 columns (e.g. `currency` $\leftrightarrow$ `currency_code`, `price_per_night` $\leftrightarrow$ `price`, `discount_type` $\leftrightarrow$ `type`, `discount_value` $\leftrightarrow$ `value`, `trip` $\leftrightarrow$ `destination`).
- **Missing API Routes Identified:** 4 endpoints (`GET /api/admin/notifications/count`, `PUT /api/admin/profile`, `PUT /api/admin/profile/password`, `POST /api/admin/upload`).
- **Missing Controller Query Filters:** Search filters on `Bookings` and `Enquiries`, `country_id` filter on `Destinations`, status filter on `Reviews`.

---

## 2. Confirmed Missing Database Columns

| Table | Existing Columns | Missing Columns | Suggested Data Type | Nullable | Reason & Frontend Origin |
| :--- | :--- | :--- | :--- | :---: | :--- |
| **`users`** | `id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `country`, `phone`, `created_at`, `updated_at` | `role` | `string` (default: `'user'`) | No (default: `'user'`) | Required by `UsersList.jsx` (`PUT /api/admin/users/:id`), `LoginPage.jsx` (`userRole`), and `ProtectedRoute.jsx` for role-based authorization. Currently hardcoded in controller based solely on email. |
| **`users`** | Same as above | `status` | `string` (default: `'active'`) | No (default: `'active'`) | Required by `UsersList.jsx` to toggle and persist user status (`Active` vs `Inactive`). |
| **`countries`** | `id`, `name`, `slug`, `flag`, `currency`, `hero_image`, `hero_title`, `tagline`, `description`, `tour_count`, `package_count`, `is_active`, `created_at`, `updated_at` | `currency_code` | `string(10)` | Yes | Required by `CountriesManager.jsx` form (`d.currency_code`, e.g. `THB`, `MYR`, `INR`). Complements existing `currency` column. |
| **`countries`** | Same as above | `currency_symbol` | `string(10)` | Yes | Required by `CountriesManager.jsx` form (`d.currency_symbol`, e.g. `฿`, `RM`, `₹`) to format pricing across regional views. |
| **`tour_packages`** | `id`, `country_id`, `slug`, `title`, `subtitle`, `image`, `badge`, `duration`, `duration_days`, `duration_nights`, `price`, `currency`, `includes`, `itinerary`, `highlights`, `terms`, `is_active`, `is_featured`, `created_at`, `updated_at` | `destination_id` | `foreignId` (FK $\rightarrow$ `destinations.id`, onDelete `'set null'`) | Yes | Required by `TourPackagesManager.jsx` destination tagging (`DestinationSelect`) and CSV bulk upload to link packages to regional destination hubs. |
| **`tour_packages`** | Same as above | `description` | `text` | Yes | Required by `TourPackagesManager.jsx` form (`<Field label="Description">`) and CSV bulk import. The table currently only has `subtitle` and `terms`. |
| **`hotels`** | `id`, `country_id`, `slug`, `title`, `location`, `image`, `category`, `stars`, `destination`, `badge`, `rating`, `reviews_count`, `price_per_night`, `currency`, `amenities`, `description`, `policies`, `is_active`, `created_at`, `updated_at` | `destination_id` | `foreignId` (FK $\rightarrow$ `destinations.id`, onDelete `'set null'`) | Yes | Required by `HotelsManager.jsx` destination tagging (`DestinationSelect`) and CSV bulk upload. The table currently only has a freeform `destination` string without relational integrity. |
| **`hotels`** | Same as above | `is_featured` | `boolean` (default: `false`) | No (default: `false`) | Required by `HotelsManager.jsx` (`is_featured` checkbox and CSV bulk import). Table was omitted from `2026_10_27_000001_add_is_featured_to_catalog_tables.php`. |
| **`transfers`** | `id`, `country_id`, `slug`, `title`, `from_location`, `to_location`, `duration`, `price`, `currency`, `image`, `vehicles`, `description`, `is_active`, `created_at`, `updated_at` | `destination_id` | `foreignId` (FK $\rightarrow$ `destinations.id`, onDelete `'set null'`) | Yes | Required by `TransfersManager.jsx` destination tagging (`DestinationSelect`) and CSV bulk upload. |
| **`reviews`** | `id`, `name`, `trip`, `rating`, `comment`, `date`, `avatar`, `is_featured`, `is_active`, `created_at`, `updated_at` | `email` | `string` | Yes | Required by `ReviewsManager.jsx` (`review.email` display in moderation panel). |
| **`reviews`** | Same as above | `destination` | `string` | Yes | Required by `ReviewsManager.jsx` (`review.destination` display). Table currently only has `trip`. |
| **`reviews`** | Same as above | `status` | `enum('pending', 'approved', 'rejected')` (default: `'pending'`) | No (default: `'pending'`) | Required by `ReviewsManager.jsx` for review moderation workflow (`Approve`, `Reject`) and topbar notification bell (`pending_reviews`). Table currently only has boolean `is_active`. |
| **`reviews`** | Same as above | `admin_note` | `text` | Yes | Required by `ReviewsManager.jsx` (`admin_note` textarea saved via `PUT /api/admin/reviews/:id`). |

---

## 3. Confirmed Missing Database Tables

**Result:** **0 missing tables.**

Every frontend functional module corresponds to an existing backend table:
1. `users` — Registered accounts & admin users.
2. `countries` — Country-level tourism data.
3. `destinations` — Regional destination hubs.
4. `day_tours` — Single-day excursion activities.
5. `tour_packages` — Multi-day package itineraries.
6. `hotels` — Accommodation listings.
7. `transfers` — Private ground transport routes.
8. `bookings` — Reservation records.
9. `enquiries` — Bespoke vacation inquiry leads.
10. `promo_codes` — Discount vouchers.
11. `reviews` — Testimonials and customer reviews.
12. `offices` — Branch offices and contact maps.
13. `settings` — Key-value site settings and brand metadata.

---

## 4. Existing Columns Reused / Field Mapping Table

To prevent redundant database columns, the following frontend fields are mapped to existing backend columns:

| Frontend Field Name | Consuming File | Existing Backend Column | Target Table | Handling / Mapping Strategy |
| :--- | :--- | :--- | :--- | :--- |
| `price` | `HotelsManager.jsx`, `HotelDetail.jsx` | `price_per_night` | `hotels` | `HotelController` merges `price` into `price_per_night` on write; `Hotel.php` provides an accessor `$hotel->price` returning `price_per_night`. |
| `type` | `PromoCodesList.jsx` | `discount_type` | `promo_codes` | `PromoCodeController` maps `$request->type` to `discount_type` if `discount_type` is not explicitly provided. |
| `value` | `PromoCodesList.jsx` | `discount_value` | `promo_codes` | `PromoCodeController` maps `$request->value` to `discount_value`. |
| `destination` | `ReviewsManager.jsx` | `trip` | `reviews` | Controller copies `destination` to `trip` or supports both fields gracefully. |
| `currency_code` | `CountriesManager.jsx` | `currency` | `countries` | Controller syncs `currency_code` with `currency` while persisting both. |

---

## 5. Missing Backend API Endpoints & Controller Logic

### 1. `GET /api/admin/notifications/count`
- **Frontend Caller:** [`AdminDashboard.jsx`](file:///e:/Eurasia%20Holidays/plantrip/PlanTripOnline-Frontend/src/pages/AdminDashboard.jsx#L404)
- **Expected Response:** `{ pending_bookings: int, pending_enquiries: int, pending_reviews: int }`
- **Solution:** Add `notificationsCount()` in `DashboardController` and register route in `routes/api.php`.

### 2. `PUT /api/admin/profile` & `PUT /api/admin/profile/password`
- **Frontend Caller:** [`AdminProfileSettings.jsx`](file:///e:/Eurasia%20Holidays/plantrip/PlanTripOnline-Frontend/src/components/admin/AdminProfileSettings.jsx#L87-L147)
- **Expected Payloads:**
  - Profile: `{ name: string }`
  - Password: `{ current_password: string, new_password: string, new_password_confirmation: string }`
- **Solution:** Add `updateProfile` and `updatePassword` in `UserController` and register routes under `auth:sanctum` and `admin` middleware.

### 3. `POST /api/admin/upload`
- **Frontend Caller:** [`ContentShared.jsx`](file:///e:/Eurasia%20Holidays/plantrip/PlanTripOnline-Frontend/src/components/admin/content/ContentShared.jsx#L399) (`ImageUpload`)
- **Expected Request:** Multipart `FormData` with `file`
- **Expected Response:** `{ url: string, path: string }`
- **Solution:** Create `UploadController` (or method in `DashboardController`) saving to `storage/app/public/uploads` and returning `{ url: asset('storage/' . $path) }`.

### 4. Controller Enhancements
- **`DashboardController@stats`:** Return missing quick stat counters expected by `AdminOverview.jsx`: `total_bookings`, `active_hotels`, `active_tours`, `pending_reviews`.
- **`BookingController@adminIndex`:** Add `search` filter on `booking_reference`, `first_name`, `last_name`, `email`, `phone`, `whatsapp`, `destination`.
- **`EnquiryController@index`:** Add `search` filter on `full_name`, `email`, `phone`, `destination`.
- **`DestinationController@index`:** Support `country_id` filter; relax image URL validation to string for local upload paths.
- **`ReviewController@adminIndex` & `update`:** Add pagination, `status` filter, search, and update handler for `status` and `admin_note`.

---

## 6. Implementation Plan & Migration Strategy

1. **Create Migrations:**
   - `2026_11_09_000001_add_role_and_status_to_users_table.php`
   - `2026_11_09_000002_add_currency_fields_to_countries_table.php`
   - `2026_11_09_000003_add_destination_and_description_to_tour_packages_table.php`
   - `2026_11_09_000004_add_destination_and_featured_to_hotels_table.php`
   - `2026_11_09_000005_add_destination_to_transfers_table.php`
   - `2026_11_09_000006_add_moderation_fields_to_reviews_table.php`
2. **Update Eloquent Models:**
   - Update `$fillable`, `$casts`, and relationships on `User`, `Country`, `TourPackage`, `Hotel`, `Transfer`, and `Review`.
3. **Update Controllers & Routes:**
   - Register the 4 missing endpoints in `routes/api.php`.
   - Update `UserController`, `DashboardController`, `DestinationController`, `HotelController`, `PromoCodeController`, and `ReviewController`.
4. **Execute & Test Migrations Safe Local Database:**
   - Run migrations against SQLite test database.
   - Verify all tests pass cleanly.

