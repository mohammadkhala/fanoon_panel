# Elite Vape DB – Code Quality & Security Audit Report

**Date:** February 28, 2025  
**Project:** elitevapeDB (Laravel)

---

## Executive Summary

| Severity | Count |
|----------|-------|
| Critical | 6 |
| Medium   | 12 |
| Low      | 15 |

---

## 1. CRITICAL ISSUES

### 1.1 Unprotected Route – Mass Currency Insertion
**File:** `elitevapeDB/routes/web.php:191-206`  
**Issue:** The `/add-currency` route is publicly accessible with no authentication. Any unauthenticated user can insert arbitrary currency data into the database.

```php
Route::get('add-currency', function () {
    $currencies = file_get_contents("installation/currency.json");
    // ... DB::table('currencies')->insert($keep);
    return response()->json(['ok']);
});
```

**Recommendation:** Remove this route or protect it with admin middleware. If needed for setup, use a one-time migration or a protected installer flow.

---

### 1.2 Hardcoded Debug Path & Sensitive Logging
**File:** `elitevapeDB/app/Http/Controllers/Api/V1/ConfigController.php:37-48, 248-262`  
**Issue:** Absolute path and debug logging to a fixed file:

```php
file_put_contents(
    '/Users/baitpait/BAITPAIT/Bait Pait/Project/elite vape/DB/.cursor/debug-914e07.log',
    json_encode([...])
);
```

**Risks:** Path disclosure, local filesystem dependency, and potential information leakage in production.

**Recommendation:** Remove debug logging or use Laravel’s logging (`Log::channel()`) with configurable paths.

---

### 1.3 Null Pointer / Missing Validation in OrderController
**File:** `elitevapeDB/app/Http/Controllers/Admin/OrderController.php:406-413`  
**Issue:** `paymentStatus()` uses `$this->order->find($request->id)` without checking for null. If the order does not exist, accessing `$order['transaction_reference']` will cause an error.

```php
$order = $this->order->find($request->id);
if ($request->payment_status == 'paid' && $order['transaction_reference'] == null ...
```

**Recommendation:** Add validation and null checks:

```php
$order = $this->order->find($request->id);
if (!$order) {
    return back()->withErrors(['Order not found']);
}
```

---

### 1.4 OTP Bypass in Development Mode
**File:** `elitevapeDB/app/Http/Controllers/Api/V1/Auth/PasswordResetController.php:64`  
**File:** `elitevapeDB/app/Http/Controllers/Api/V1/Auth/CustomerAuthController.php:125, 303`  
**Issue:** Hardcoded OTP `123456` when `APP_MODE` is not `live`:

```php
$token = (env('APP_MODE') == 'live') ? rand(100000, 999999) : 123456;
```

**Recommendation:** Remove dev bypass or restrict it to local/test environments only. Ensure `APP_MODE=live` in production.

---

### 1.5 Overly Broad CSRF Exemptions
**File:** `elitevapeDB/app/Http/Middleware/VerifyCsrfToken.php:14-17`  
**Issue:** Broad CSRF exemptions:

```php
protected $except = [
    '/pay-via-ajax', '/success','/cancel','/fail','/ipn','/payment-razor','/bkash/*','/system_settings','sslcommerz/*',
    '/database_installation', '/purchase_code',
];
```

**Risks:** `/system_settings` and `/database_installation` are exempt from CSRF. If these are reachable, they can be abused for CSRF attacks.

**Recommendation:** Limit exemptions to payment callbacks and other strictly necessary endpoints. Protect sensitive endpoints with authentication and other safeguards.

---

### 1.6 Potential Path Traversal in UpdateController
**File:** `elitevapeDB/app/Http/Controllers/UpdateController.php:250-256`  
**Issue:** User-controlled `$request['path']` is used in file paths:

```php
$sql = File::get(base_path($request['path'] . 'database/addon_settings.sql'));
```

**Note:** Update routes are currently commented out in `RouteServiceProvider.php`, so this is not active. If enabled, it would be a path traversal risk.

**Recommendation:** If update routes are re-enabled, validate and sanitize `path` (e.g. allowlist, no `..`, restrict to known subpaths).

---

## 2. MEDIUM ISSUES

### 2.1 XSS via Unescaped HTML Output
**Files:**
- `elitevapeDB/resources/views/admin-views/product/edit.blade.php:73, 92`
- `elitevapeDB/resources/views/admin-views/product/view.blade.php:140`
- `elitevapeDB/resources/views/admin-views/business-settings/*.blade.php` (return_page, refund_page, cancellation_page, privacy-policy, terms-and-conditions, about-us)

**Issue:** `{!! $product['description'] !!}` and similar directives output raw HTML. If descriptions or other content come from untrusted input, this can lead to stored XSS.

**Recommendation:** Use `{{ }}` for user content, or sanitize with a library like HTMLPurifier before output. Reserve `{!! !!}` only for trusted, sanitized HTML.

---

### 2.2 Missing Input Validation – addPaymentReferenceCode
**File:** `elitevapeDB/app/Http/Controllers/Admin/OrderController.php:476-484`  
**Issue:** `transaction_reference` is taken directly from the request without validation:

```php
$this->order->where(['id' => $id])->update([
    'transaction_reference' => $request['transaction_reference']
]);
```

**Recommendation:** Validate and sanitize `transaction_reference` (e.g. max length, allowed characters).

---

### 2.3 File Upload – uploadFile Extension Handling
**File:** `elitevapeDB/app/CentralLogics/Helpers.php:1007-1019`  
**Issue:** `uploadFile()` uses `$file->getClientOriginalExtension()` without validation. In theory, this could allow dangerous extensions if used for non-image uploads.

```php
$fileName = date('Y-m-d') . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
$file->storeAs("public/{$dir}", $fileName);
```

**Note:** Current callers (e.g. Apple service file) validate extensions. Risk is lower but still worth tightening.

**Recommendation:** Add an allowlist of extensions and validate before saving.

---

### 2.4 APP_DEBUG in .env.example
**File:** `elitevapeDB/.env.example:4`  
**Issue:** `APP_DEBUG=true` in the example file. If copied to production, it can expose stack traces and sensitive data.

**Recommendation:** Set `APP_DEBUG=false` in `.env.example` and document that production must use `false`.

---

### 2.5 Direct env() Usage
**Files:** `elitevapeDB/app/Http/Controllers/Api/V1/ConfigController.php:187, 279, 308`  
**Issue:** `env('SOFTWARE_VERSION')`, `env('APP_ENV')`, etc. are used directly instead of `config()`.

**Recommendation:** Use `config()` for application config and avoid `env()` in controllers.

---

### 2.6 Order Model – Duplicate Fillable
**File:** `elitevapeDB/app/Models/Order.php:10-39`  
**Issue:** `additional_payment_amount` appears twice in `$fillable`.

**Recommendation:** Remove the duplicate entry.

---

### 2.7 paymentStatus – Missing Request Validation
**File:** `elitevapeDB/app/Http/Controllers/Admin/OrderController.php:404-415`  
**Issue:** `$request->id` and `$request->payment_status` are not validated. Invalid or missing values can cause errors or unexpected behavior.

**Recommendation:** Add validation:

```php
$request->validate([
    'id' => 'required|integer|exists:orders,id',
    'payment_status' => 'required|in:paid,unpaid',
]);
```

---

### 2.8 updateShipping – No Address Ownership Check
**File:** `elitevapeDB/app/Http/Controllers/Admin/OrderController.php:422-444`  
**Issue:** Address is updated by ID without verifying it belongs to the order being edited. For admin context this may be acceptable, but it weakens defense in depth.

**Recommendation:** Optionally verify that the address is linked to the order before updating.

---

### 2.9 Captcha Bypass When Recaptcha Disabled
**File:** `elitevapeDB/app/Http/Controllers/Admin/Auth/LoginController.php:82-111`  
**Issue:** If recaptcha is disabled and `default_captcha_value` is not sent, no captcha is enforced. Login can proceed without captcha.

**Recommendation:** Require either recaptcha or default captcha when configured.

---

### 2.10 Duplicate Route Definition
**File:** `elitevapeDB/routes/admin.php:117-118`  
**Issue:** The `/add` route is defined twice for `CategoryController::store`.

**Recommendation:** Remove the duplicate route definition.

---

### 2.11 API Rate Limiting
**File:** `elitevapeDB/bootstrap/app.php:56`  
**Issue:** API uses `throttle:60,1` (60 requests/minute). This may be too permissive for sensitive endpoints (e.g. auth, password reset).

**Recommendation:** Use stricter throttling for auth-related endpoints (e.g. auth, password reset, OTP).

---

### 2.12 AuthenticateSession Disabled
**File:** `elitevapeDB/bootstrap/app.php:49`  
**Issue:** `AuthenticateSession` middleware is commented out in the web group. This can reduce protection against session fixation.

**Recommendation:** Re-enable `AuthenticateSession` for the web middleware group if it is supported by your Laravel version.

---

## 3. LOW / RECOMMENDATIONS

### 3.1 Dead Code
**File:** `elitevapeDB/app/Http/Controllers/Admin/OrderController.php:356-358`  
**Issue:** Duplicate return statement:

```php
if ($delivery_man_id == 0) {
    return response()->json([], 401);
    return response()->json([], 401);  // Duplicate
}
```

---

### 3.2 SQL Raw Queries
**Files:** `ProductController.php:221-230`, `OrderController.php`, `SystemController.php`  
**Issue:** Several `raw()` and `whereRaw()` usages. Most use parameter binding correctly; ensure no user input is concatenated into raw SQL.

**Recommendation:** Prefer Eloquent/Query Builder where possible; for raw SQL, always use parameter binding.

---

### 3.3 Pagination Links – {!! !!}
**Files:** Various blade files (e.g. `product/list.blade.php`, `order/list.blade.php`)  
**Issue:** `{!! $products->links() !!}` outputs unescaped HTML. Laravel’s pagination HTML is usually safe, but `{!! !!}` is a pattern that can be risky if misused.

**Recommendation:** Prefer `{{ $products->links() }}` if it meets your needs, or ensure pagination output is always safe.

---

### 3.4 Error Handling
**File:** `elitevapeDB/app/Exceptions/Handler.php`  
**Issue:** Default exception handling with no custom logic for production (e.g. no generic error page, no logging customization).

**Recommendation:** Add production-friendly error handling and logging.

---

### 3.5 Naming Conventions
**File:** `elitevapeDB/bootstrap/app.php:82`  
**Issue:** Middleware alias `actch` appears to be a typo for `activation-check` or similar.

**Recommendation:** Rename to a clearer alias (e.g. `activation-check`).

---

### 3.6 Image Proxy – SSRF Considerations
**File:** `elitevapeDB/routes/web.php:19-81`  
**Issue:** Image proxy fetches external URLs. Current checks reduce risk, but SSRF remains a concern for any URL fetcher.

**Recommendation:** Add URL blocklists (e.g. internal IPs, localhost) and consider a timeout/redirect limit.

---

### 3.7 Database Credentials in .env.example
**File:** `elitevapeDB/.env.example:13-14`  
**Issue:** Example shows `DB_DATABASE=hexacom_test` and empty password. Ensure production uses strong credentials and that `.env` is never committed.

---

### 3.8 API Performance Debug Middleware
**File:** `elitevapeDB/bootstrap/app.php:56`  
**Issue:** `ApiPerformanceDebugMiddleware` runs on all API requests. If it logs or does heavy work, it can affect performance and privacy.

**Recommendation:** Disable or restrict this middleware in production.

---

### 3.9 Update/Install Routes
**File:** `elitevapeDB/app/Providers/RouteServiceProvider.php:55-56`  
**Issue:** `mapUpdateRoutes()` and `mapInstallRoutes()` are commented out. This is good for security, but the code remains in the codebase.

**Recommendation:** Remove or clearly isolate installer/update code from production deployments.

---

### 3.10 ProductController – foreach on request->all()
**File:** `elitevapeDB/app/Http/Controllers/Admin/ProductController.php:324, 614`  
**Issue:** `foreach ($request->all() as $key => $value)` iterates over all request data. Ensure only intended keys are processed to avoid mass assignment or unexpected behavior.

---

## 4. SECURITY CHECKLIST SUMMARY

| Category                    | Status | Notes                                              |
|----------------------------|--------|----------------------------------------------------|
| Authentication              | OK     | Admin/Branch middleware in use                     |
| Authorization               | OK     | Routes protected by middleware                     |
| Input Validation            | Partial| Several endpoints lack validation                  |
| SQL Injection               | OK     | Parameterized queries used; raw usage reviewed    |
| XSS                         | Risk   | Unescaped output in product/business-settings     |
| CSRF                        | Partial| Broad exemptions; payment callbacks exempted       |
| Sensitive Data Exposure     | Risk   | Debug logging, APP_DEBUG, env usage                |
| File Upload Validation      | OK     | Images validated; uploadFile could be stricter     |
| Mass Assignment             | OK     | Models use `$fillable`                             |

---

## 5. RECOMMENDED PRIORITY ACTIONS

1. **Immediate:** Remove or protect `/add-currency` route.
2. **Immediate:** Remove hardcoded debug logging from `ConfigController`.
3. **Immediate:** Add null checks and validation in `OrderController::paymentStatus()`.
4. **Short-term:** Sanitize or escape product descriptions and similar content to prevent XSS.
5. **Short-term:** Set `APP_DEBUG=false` in `.env.example` and ensure production uses it.
6. **Short-term:** Narrow CSRF exemptions and re-evaluate `/system_settings` and `/database_installation`.
7. **Medium-term:** Remove OTP bypass in non-live environments or restrict to local/test only.
8. **Medium-term:** Add validation for `addPaymentReferenceCode` and other sensitive endpoints.

---

*Report generated by automated code audit.*
