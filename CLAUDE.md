# Project: Reenson / Apex POS

## Server
- **Host:** apexpos.co.ke (Hostinger)
- **SSH:** `ssh u856697070@apexpos.co.ke -p 65002`
- **App path:** `/home/u856697070/domains/apexpos.co.ke/public_html/serengeti`
- **Deploy pattern:**
  ```bash
  cd ~/domains/apexpos.co.ke/public_html/serengeti
  git fetch origin
  git checkout origin/dev -- <file(s)>
  php artisan cache:clear && php artisan route:clear
  ```
- When the user says "deploy", "push to server", or mentions a folder/domain, always use the path above unless they specify a different folder.

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

## Hard rules — always follow
- **NEVER create a new GitHub repository.** The repo already exists at `https://github.com/mirierimich44-design/cedar`. Always push to `origin` on the `dev` branch. Never run `gh repo create` or `git init` + new remote.
- **NEVER use a git worktree** unless explicitly asked. Work directly in `C:\laragon\www\reenson`.
- Deploy to server by SSHing in and running `git fetch origin && git checkout origin/dev -- <file>` — not by pushing to a separate branch or creating PRs.

## Key facts
- `variation_location_details.qty_available` is the stock field — decremented via `ProductUtil::decreaseProductQuantity()`
- `TransactionUtil::createOrUpdateSellLines()` does NOT deduct stock — must call `decreaseProductQuantity()` separately after
- `App\BusinessPaymentMethod` model does NOT exist — payment types are hardcoded in `MobilePosController::paymentTypes()`
- Log files are named by date: `storage/logs/laravel-YYYY-MM-DD.log` (LOG_CHANNEL=daily)
- All mobile API routes live in `routes/api.php` under `Route::prefix('mobile')`
- `posDetails` returns `is_admin` flag and full `locations` list
