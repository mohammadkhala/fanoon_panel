# Elite Vape DB – Fresh Code Quality & Security Audit Report

**Date:** February 28, 2025  
**Project:** elitevapeDB (Laravel)  
**Scope:** Comprehensive audit including verification of previous fixes

---

## Executive Summary

| Severity | Count |
|----------|-------|
| Critical | 3 |
| Medium   | 8 |
| Low      | 10 |

---

## 1. VERIFICATION OF PREVIOUS FIXES

### ✅ OTP Fix – Correctly Implemented
**Files:** `CustomerAuthController.php`, `PasswordResetController.php`  
**Status:** Fixed. OTP now uses `config('app.env') === 'local'` for dev bypass (123456). Production uses `rand(100000, 999999)`.

### ✅ CSRF Exemptions – Narrowed
**File:** `VerifyCsrfToken.php:14-17`  
**Status:** Fixed. Exemptions limited to payment callbacks: `/pay-via-ajax`, `/success`, `/cancel`, `/fail`, `/ipn`, `/payment-razor`, `/bkash/*`, `sslcommerz/*`. Installation routes removed.

### ✅ uploadFile – Extension Validation
**File:** `Helpers.php:1023-1044`  
**Status:** Fixed. `uploadFile()` enforces allowed extensions with default whitelist `['jpg','jpeg','png','gif','webp','pdf','doc','docx','p8','p12','pem','json','txt']`. Throws `InvalidArgumentException` for disallowed extensions.

### ✅ env/config – Partially Fixed
**Files:** `ConfigController.php`, `config/app.php`  
**Status:** Partially fixed. Controllers use `config()` for app.env, software_version. **Remaining:** Many Blade views still use `env('APP_MODE')` directly (see Medium issues).

### ✅ Product Description XSS – Partially Fixed
**Files:** `product/view.blade.php`, `product/edit.blade.php`  
**Status:** Fixed for product description. Uses `Helpers::sanitizeHtmlForDisplay()`. **Remaining:** Other pages (return_page, refund_page, etc.) and `trimWords` output are not sanitized.

### ✅ Payment Status Validation – Fixed
**File:** `OrderController.php`  
**Status:** Fixed. `paymentStatus()` and `addPaymentReferenceCode` have proper validation and null checks.

### ✅ Image Upload – Validated
**Files:** `ProductController.php`, `BannerController.php`, `AddonController.php`  
**Status:** Fixed. Uses `UploadSizeHelperTrait`, `validateUploadedFile()`, and `mimes`/`extensions` validation.

---

## 2. CRITICAL ISSUES

### 2.1 Path Traversal in AddonController (publish, activation, deleteAddon)
**File:** `app/Http/Controllers/Admin/System/AddonController.php`  
**Lines:** 103-116, 128-161, 167-186

**Issue:** User-controlled `$request['path']` and `$request->path` are used in:
- `include($request['path'] . '/Addon/info.php')` – arbitrary file inclusion
- `file_put_contents(base_path($request['path'] . '/Addon/info.php'), ...)` – arbitrary file write
- `File::deleteDirectory(base_path($path))` – arbitrary directory deletion

**Example:** `path=../../../etc/passwd` or `path=../app/Http/Controllers` could lead to RCE or data loss.

**Recommendation:** Validate path against known addon list (e.g. `Modules/*/Addon/info.php`). Use `realpath()` and ensure result is under `base_path('Modules')`.

---

### 2.2 OTP in Logs (Sensitive Data Exposure)
**Files:**  
- `app/Http/Controllers/Api/V1/Auth/PasswordResetController.php:95`  
- `app/Http/Controllers/Api/V1/Auth/CustomerAuthController.php:313`

**Issue:** OTP values are logged in local mode:
```php
Log::info('Password reset OTP (local mode – not sent by email)', ['email' => $customer['email'], 'otp' => $token]);
Log::info('Email verification OTP (local mode – not sent by email)', ['email' => $request['email'], 'otp' => $token]);
```

**Risk:** If logs are misconfigured, exposed, or rotated poorly, OTPs can be leaked. Logs may persist in production.

**Recommendation:** Remove OTP from logs. Log only `['email' => '...', 'event' => 'otp_sent']` without the token.

---

### 2.3 updateOtp – No Input Validation
**File:** `app/Http/Controllers/Admin/BusinessSettingsController.php:1305-1326`

**Issue:** `updateOtp()` accepts raw request values without validation:
```php
$this->InsertOrUpdateBusinessData(['key' => 'maximum_otp_hit'], ['value' => $request['maximum_otp_hit']]);
$this->InsertOrUpdateBusinessData(['key' => 'otp_resend_time'], ['value' => $request['otp_resend_time']]);
// ... etc
```

**Risk:** Invalid values (negative numbers, huge integers, non-numeric) can break OTP logic or cause denial of service.

**Recommendation:** Add validation:
```php
$request->validate([
    'maximum_otp_hit' => 'required|integer|min:1|max:20',
    'otp_resend_time' => 'required|integer|min:30|max:600',
    'temporary_block_time' => 'required|integer|min:60|max:3600',
    // ...
]);
```

---

## 3. MEDIUM ISSUES

### 3.1 XSS – contentByLang in Business Settings Pages
**Files:**  
- `resources/views/admin-views/business-settings/return_page-index.blade.php:60`  
- `resources/views/admin-views/business-settings/refund_page-index.blade.php:58`  
- `resources/views/admin-views/business-settings/cancellation_page-index.blade.php:59`  
- `resources/views/admin-views/business-settings/privacy-policy.blade.php:50`  
- `resources/views/admin-views/business-settings/terms-and-conditions.blade.php:50`  
- `resources/views/admin-views/business-settings/about-us.blade.php:50`

**Issue:** `{!! $contentByLang[$lang] ?? '' !!}` outputs raw HTML with no sanitization. Admin-edited content can contain XSS if stored without sanitization.

**Recommendation:** Use `Helpers::sanitizeHtmlForDisplay($contentByLang[$lang] ?? '')` before output.

---

### 3.2 XSS – trimWords Output (Product Description)
**Files:**  
- `resources/views/branch-views/pos/_quick-view-data.blade.php:54, 116, 121`  
- `resources/views/admin-views/pos/_quick-view-data.blade.php:54, 116, 121`

**Issue:** `Helpers::trimWords()` uses `strip_tags()` but does not sanitize HTML entities. Content like `&#60;script&#62;alert(1)&#60;/script&#62;` can pass through and execute when injected via `.html()` in JavaScript.

**Recommendation:** Use `Helpers::sanitizeHtmlForDisplay()` on the trimmed text, or ensure `trimWords` output is passed to `.text()` instead of `.html()` in JS.

---

### 3.3 XSS – getCategories (Category Name)
**File:** `app/Http/Controllers/Admin/ProductController.php:83-96`

**Issue:** Category names returned in HTML are concatenated without escaping:
```php
$res .= '<option value="' . $row->id . '">' . $row->name . '</option>';
```

**Risk:** Admin-created category names with `<script>`, `"onclick="`, etc. can cause XSS when the response is rendered.

**Recommendation:** Use `e($row->name)` or `htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8')` when building the HTML.

---

### 3.4 env() in Blade Views
**Files:**  
- `resources/views/layouts/admin/app.blade.php:36, 387`  
- `resources/views/admin-views/auth/login.blade.php:84, 149`  
- `resources/views/admin-views/business-settings/*.blade.php` (multiple)  
- `resources/views/admin-views/settings.blade.php:208, 216`  
- `resources/views/admin-views/business-settings/mail-index.blade.php:77-126`  
- etc.

**Issue:** `env('APP_MODE')` is used directly in views. In production, `env()` may be cached and can cause issues. Best practice is to use `config('app.mode')` or similar.

**Recommendation:** Add `'mode' => env('APP_MODE', 'live')` to `config/app.php` and use `config('app.mode')` in views.

---

### 3.5 Addon Zip Upload – Zip Slip / Path Traversal
**File:** `app/Http/Controllers/Admin/System/AddonController.php:68-70`

**Issue:** Zip extraction uses `$zip->extractTo($extractPath)` without validating entries. Malicious zip with `../` in filenames can write outside `Modules/`.

**Recommendation:** Validate each entry path before extraction:
```php
$extractPath = realpath(base_path('Modules'));
foreach ($zip entries) {
    $resolved = realpath($extractPath . '/' . $entry);
    if (strpos($resolved, $extractPath) !== 0) throw new \Exception('Invalid path');
}
```

---

### 3.6 Product Search – Potential SQL Injection (Low Risk)
**File:** `app/Http/Controllers/Admin/ProductController.php:254-258`

**Issue:** Search terms are used in `like`:
```php
$q->orWhere('name', 'like', "%{$value}%");
```
Laravel uses parameter binding, so this is safe. **Verified:** No SQL injection. The `$value` is passed as a bound parameter.

---

### 3.7 Apple Login – uploadFile with p8 Only
**File:** `app/Http/Controllers/Admin/BusinessSettingsController.php:1399-1409`

**Issue:** Validation uses `extensions:p8` but `uploadFile()` default whitelist includes p8. **Status:** Correct. `uploadFile` is called with `$request->file('service_file')`; validation ensures p8. No override of allowed extensions passed.

---

### 3.8 ProductController – whereRaw Usage
**File:** `app/Http/Controllers/Admin/ProductController.php:221-231`

**Issue:** `whereRaw` with JSON_SEARCH. **Verified:** Uses parameter binding `[(string) $categoryId]` – safe.

---

## 4. LOW ISSUES

### 4.1 Duplicate Code – OTP Logic
**Files:** `CustomerAuthController.php`, `PasswordResetController.php`  
**Issue:** OTP rate limiting, block time, and hit count logic are duplicated across phone/email/verifyOTP. Consider extracting to a shared service.

---

### 4.2 Naming – getEarningStatitics
**File:** `app/Http/Controllers/Admin/SystemController.php`  
**Issue:** Typo: `getEarningStatitics` should be `getEarningStatistics`.

---

### 4.3 Error Handling – AddonController
**File:** `app/Http/Controllers/Admin/System/AddonController.php`  
**Issue:** `include()` and `file_put_contents()` can throw. Consider try/catch around file operations.

---

### 4.4 Toastr::message() – Raw Output
**Files:** `admin-views/auth/login.blade.php:114`, `layouts/admin/app.blade.php:154`  
**Issue:** `{!! Toastr::message() !!}` outputs raw HTML. Toastr typically generates safe HTML; verify it does not output user-controlled content without escaping.

---

### 4.5 setEnvironmentValue – env() in Runtime
**File:** `app/CentralLogics/Helpers.php:1065-1072`  
**Issue:** `env($envKey)` used when modifying .env. Ensure this is only used during installation/setup, not in normal request handling.

---

### 4.6 FORCE_HTTPS – env() in AppServiceProvider
**File:** `app/Providers/AppServiceProvider.php:51`  
**Issue:** `env('FORCE_HTTPS')` in boot. Consider moving to config.

---

### 4.7 Image Proxy – Same-Origin Check
**File:** `routes/web.php:19-81`  
**Status:** Well-implemented. Path validation, `realpath` check, and MIME type handling are correct.

---

### 4.8 API Throttle
**File:** `bootstrap/app.php:56`  
**Status:** API uses `throttle:60,1`. Adequate for most use cases.

---

### 4.9 Missing API Authentication Check
**Note:** API routes use `auth` middleware where needed. Verify all sensitive endpoints (e.g. order creation, customer data) require authentication.

---

### 4.10 Choice Options – XSS in Admin
**File:** `resources/views/branch-views/pos/_quick-view-data.blade.php:69-76`  
**Issue:** `$option` from `$choice->options` is output in `value="{{ $option }}"` and `for="{{ $choice->name }}-{{ $option }}"`. If options contain quotes or HTML, XSS is possible. Ensure options are validated/sanitized on product save.

---

## 5. SUMMARY TABLE

| # | Severity | Issue | File:Line | Status |
|---|----------|-------|-----------|--------|
| 1 | Critical | Path traversal in AddonController | AddonController.php:103-186 | **Remains** |
| 2 | Critical | OTP logged in logs | PasswordResetController.php:95, CustomerAuthController.php:313 | **Remains** |
| 3 | Critical | updateOtp no validation | BusinessSettingsController.php:1305-1326 | **Remains** |
| 4 | Medium | XSS contentByLang | return_page-index.blade.php:60, etc. | **Remains** |
| 5 | Medium | XSS trimWords | _quick-view-data.blade.php:54, 116, 121 | **Remains** |
| 6 | Medium | XSS getCategories | ProductController.php:86-91 | **Remains** |
| 7 | Medium | env() in views | app.blade.php:36, 387, etc. | **Remains** |
| 8 | Medium | Zip Slip in addon upload | AddonController.php:68-70 | **Remains** |
| 9 | Low | OTP logic duplication | CustomerAuthController, PasswordResetController | **Remains** |
| 10 | Low | Typo getEarningStatitics | SystemController.php | **Remains** |

---

## 6. PREVIOUSLY FIXED (Verified)

- OTP bypass (env vs config)
- CSRF exemptions narrowed
- uploadFile extension validation
- Product description sanitization (product view/edit)
- config() vs env() in ConfigController
- paymentStatus validation
- addPaymentReferenceCode validation
- Order model duplicate fillable
- Upload size validation (UploadSizeHelperTrait)
- activation-check middleware name fix

---

## 7. RECOMMENDATIONS PRIORITY

1. **Immediate:** Fix AddonController path traversal and validate all `path` inputs.
2. **Immediate:** Remove OTP from logs.
3. **Immediate:** Add validation to `updateOtp()`.
4. **Short-term:** Sanitize `contentByLang` and `trimWords` output for XSS.
5. **Short-term:** Escape category names in `getCategories`.
6. **Medium-term:** Replace `env()` in views with `config()`.
7. **Medium-term:** Add Zip Slip protection to addon upload.
8. **Long-term:** Refactor OTP logic into a shared service.
