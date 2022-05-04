<?php

namespace TwoFactorAuth;

use Illuminate\Support\Facades\Route;
use TwoFactorAuth\Facades\AuthFacade;
use Illuminate\Support\ServiceProvider;
use TwoFactorAuth\Facades\TokenStoreFacade;
use TwoFactorAuth\Facades\TokenSenderFacade;
use TwoFactorAuth\Authenticator\SessionAuth;
use TwoFactorAuth\Facades\UserProviderFacade;
use TwoFactorAuth\Facades\TokenGeneratorFacade;

class TwoFactorAuthServiceProvider extends ServiceProvider
{
    private $namespace = 'TwoFactorAuth\Http\Controllers';

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/two_factor_auth_config.php',
            'two_factor_config'
        );





        AuthFacade::shouldProxyTo(SessionAuth::class);
        UserProviderFacade::shouldProxyTo(UserProvider::class);
        if (app()->runningUnitTests()) {
            $tokenGenerator = FakeTokenGenerator::class;
            $tokenStore = FakeTokenStore::class;
            $tokenSender = FakeTokenSender::class;
        } else {
            $tokenSender = TokenSender::class;
            $tokenGenerator = TokenGenerator::class;
            $tokenStore = TokenStore::class;
        }
        TokenGeneratorFacade::shouldProxyTo($tokenGenerator);
        TokenStoreFacade::shouldProxyTo($tokenStore);
        TokenSenderFacade::shouldProxyTo($tokenSender);

    }

    public function boot()
    {
        $this->defineRoutes();
    }

    private function defineRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(__DIR__.'./routes.php');
    }
}
