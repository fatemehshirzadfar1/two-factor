<?php

namespace TwoFactorAuth\Http\Responses;

use Illuminate\Http\Response;

class AndroidResponses
{
    public function emailNotValid()
    {

    }

    public function blockedUser()
    {
        return response()->json(
            ['err' => 'The account is blocked'], Response::HTTP_BAD_REQUEST
        );
    }

    public function tokenSent()
    {
        return response()->json(['msg' => 'token was sent to your app.']);
    }

    public function userNotFound()
    {
        return response()->json(
            ['error' => 'Email Does not Exist'], Response::HTTP_BAD_REQUEST
        );
    }
}







