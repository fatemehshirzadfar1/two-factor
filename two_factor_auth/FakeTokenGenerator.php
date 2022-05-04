<?php

namespace TwoFactorAuth;

class FakeTokenGenerator
{
    function generateToken()
    {
        return 123456;
    }
}