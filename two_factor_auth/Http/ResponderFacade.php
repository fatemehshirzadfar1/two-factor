<?php

namespace TwoFactorAuth\Http;

use Illuminate\Support\Facades\Facade;
use TwoFactorAuth\Http\Responses\VueResponses;
use TwoFactorAuth\Http\Responses\AndroidResponses;

class ResponderFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        if (request('client') == 'android') {
           return AndroidResponses::class;
        }

        return VueResponses::class;
    }

}


