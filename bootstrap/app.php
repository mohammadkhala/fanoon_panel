<?php

use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\{ActivationCheckMiddleware,
    AdminMiddleware,
    Authenticate,
    BranchMiddleware,
    CollapseDuplicatePathSlashes,
    EncryptCookies,
    GuestUser,
    InstallationMiddleware,
    localization,
    MaintenanceModeMiddleware,
    RedirectIfAuthenticated,
    VerifyCsrfToken};
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->use([
//            TrustHosts::class,
            TrustProxies::class,
            HandleCors::class,
            // بعد HandleCors حتى تُضاف ترويسات CORS على استجابة إعادة التوجيه 301
            CollapseDuplicatePathSlashes::class,
            PreventRequestsDuringMaintenance::class,
            ValidatePostSize::class,
            TrimStrings::class,
            ConvertEmptyStringsToNull::class,
        ]);
        $middleware->group('web', [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
        ]);
        $middleware->group('api', [
//           EnsureFrontendRequestsAreStateful::class,
            'throttle:60,1',
            SubstituteBindings::class,
        ]);
        /*
        |--------------------------------------------------------------------------
        | Route Middleware (Aliases)
        |--------------------------------------------------------------------------
        */
        $middleware->alias([
            'auth' => Authenticate::class,
            'auth.basic' => AuthenticateWithBasicAuth::class,
            'bindings' => SubstituteBindings::class,
            'cache.headers' => SetCacheHeaders::class,
            'can' => Authorize::class,
            'guest' => RedirectIfAuthenticated::class,
            'password.confirm' => RequirePassword::class,
            'signed' => ValidateSignature::class,
            'throttle' => ThrottleRequests::class,
            'verified' => EnsureEmailIsVerified::class,

            // Custom middlewares
            'admin'=>AdminMiddleware::class,
            'branch'=>BranchMiddleware::class,
            'localization' => localization::class,
            'installation-check' => InstallationMiddleware::class,
            'activation-check' => ActivationCheckMiddleware::class,
            'maintenance_mode' => MaintenanceModeMiddleware::class,
            'guest_user' => GuestUser::class,
            'optional_auth_api' => \App\Http\Middleware\OptionalAuthApi::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // You can customize exception handling here if needed
    })
    ->create();

$app->singleton(
    \Illuminate\Contracts\Debug\ExceptionHandler::class,
    \App\Exceptions\Handler::class
);

return $app;
