# Project: Reenson / Apex POS

## Server
- **URL:** apexpos.co.ke
- **App folder:** serengeti (i.e. `/home/.../serengeti` or `/var/www/serengeti` — confirm exact path on first SSH)
- **Deploy pattern:** `cd /path/to/serengeti && git fetch origin && git checkout origin/dev -- <file> && php artisan cache:clear && php artisan route:clear`

## Stack
- **Backend:** Laravel (UltimatePOS), PHP, MySQL
- **Mobile app:** React Native (Expo) — located at `pos-app/`
- **Auth:** Laravel Passport — Bearer token only, NO session/CSRF on API routes
- **Branch:** `dev` is the main working branch; always commit and push to `dev`

## Mobile App
- **App name:** Apex POS
- **EAS project:** `74c90242-253e-4fd3-b428-74bdf8a03052`
- **OTA updates:** `eas update --branch preview --message "..."`
- **APK builds:** `eas build -p android --profile preview`
- **API base URL:** configured in `pos-app/src/api/client.js`

## Key facts
- `variation_location_details.qty_available` is the stock field — decremented via `ProductUtil::decreaseProductQuantity()`
- `TransactionUtil::createOrUpdateSellLines()` does NOT deduct stock — must call `decreaseProductQuantity()` separately after
- `App\BusinessPaymentMethod` model does NOT exist — payment types are hardcoded in `MobilePosController::paymentTypes()`
- Log files are named by date: `storage/logs/laravel-YYYY-MM-DD.log` (LOG_CHANNEL=daily)
- All mobile API routes live in `routes/api.php` under `Route::prefix('mobile')`
- `posDetails` returns `is_admin` flag and full `locations` list
