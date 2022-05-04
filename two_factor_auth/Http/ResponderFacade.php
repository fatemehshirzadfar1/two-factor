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

//     <?php

// namespace App\Http\Controllers\V1;

// use App\Http\Controllers\Controller;

// use App\ProtectionLayers\FaildeResponses;

// use Illuminate\Support\Facades\Validator;

// class LoginController extends Controller
// {
//     public function identifyNationalIdNumber()
//     {
//         request('national_id_number');
//         //CheckUserIsGuest::run();
//         
        

//         //find user row in DB or Fail
//         // $user = UserProviderFacade::getUserByNationalIdNumber($national_id_number)->getOrSend(
//         //     [FaildeResponses::userNotFound()]
//         // );

//         // stop block users
//         // if (UserProviderFacade::isBanned($user->id)) {
//         //     return ResponderFacade::blockedUser();

//         //if first login redirect to password reset else enter password
//           //  if (UserProviderFacade::isFirstLogin()) {}
        
            
//         //4.validate password
//         // $this->validatePasswordIsValid();
//         //5.create token
//     }
// }

}


