<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {

    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        
        /*
         * Added by dao.tran for OAuth2 authentication 
         * This will catch any OAuth error and respond appropriately.
         */
        \LucaDegasperi\OAuth2Server\Middleware\OAuthExceptionHandlerMiddleware::class,
        
        // In order to make some the authorization and resource server work correctly with Laravel5,
        // remove/disable \App\Http\Middleware\VerifyCsrfToken::class        
        //\App\Http\Middleware\VerifyCsrfToken::class,
        
        \App\Http\Middleware\App::class,
        
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'admin' => \App\Http\Middleware\IsAdmin::class,
        'redac' => \App\Http\Middleware\IsRedactor::class,
        'ajax' => \App\Http\Middleware\IsAjax::class,
        
        /*
         * Added by dao.tran for OAuth2 authentication 
         * This will catch any OAuth error and respond appropriately.
         */
        'oauth' => \LucaDegasperi\OAuth2Server\Middleware\OAuthMiddleware::class,
        'oauth-user' => \LucaDegasperi\OAuth2Server\Middleware\OAuthUserOwnerMiddleware::class,
        'oauth-client' => \LucaDegasperi\OAuth2Server\Middleware\OAuthClientOwnerMiddleware::class,
        'check-authorization-params' => \LucaDegasperi\OAuth2Server\Middleware\CheckAuthCodeRequestMiddleware::class,
        'csrf' => \App\Http\Middleware\VerifyCsrfToken::class,
        
        /* added by dao.tran for custom authentication, display Authentication header at browsers */
        'auth.token' => \App\Http\Middleware\TokenAuthMiddleware::class
    ];

}
