<?php

namespace TwoFactorAuth\tests;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use TwoFactorAuth\Facades\AuthFacade;
use TwoFactorAuth\Http\ResponderFacade;
use TwoFactorAuth\Facades\TokenStoreFacade;
use TwoFactorAuth\Facades\TokenSenderFacade;
use TwoFactorAuth\Facades\UserProviderFacade;
use TwoFactorAuth\Facades\TokenGeneratorFacade;

class TwoFactorAuthTokenTest extends TestCase
{
    public function test_the_happy_path()
    {
        User::unguard();
        UserProviderFacade::shouldReceive('isBanned')
            ->once()
            ->with(1)
            ->andReturn(false);

        $user = new User(['id'=> 1, 'email' => 'fatemeh@gmail.com']);
        UserProviderFacade::shouldReceive('getUserByEmail')
            ->once()
            ->with('fatemeh@gmail.com')
            ->andReturn(nullable($user));
        // mock
        TokenGeneratorFacade::shouldReceive('generateToken')
            ->once()
            ->withNoArgs()
            ->andReturn('1q2w3e');

        TokenStoreFacade::shouldReceive('saveToken')->once()
            ->with('1q2w3e', $user->id);

        TokenSenderFacade::shouldReceive('send')->once()
            ->with('1q2w3e', $user);

        ResponderFacade::shouldReceive('tokenSent')->once();
        $this->get('/api/two-factor-auth/request-token?email=fatemeh@gmail.com');
    }

    public function test_android_responses()
    {
        User::unguard();
        UserProviderFacade::shouldReceive('isBanned')
            ->andReturn(false);

        $user = new User(['id'=> 1, 'email' => 'fatemeh@gmail.com']);
        UserProviderFacade::shouldReceive('getUserByEmail')
            ->andReturn(nullable($user));

        $response = $this->get('/api/two-factor-auth/request-token?email=fatemeh@gmail.com&client=android');
        $response->assertJson(['msg' => 'token was sent to your app.']);
    }

    public function test_user_is_banned()
    {
        User::unguard();
        UserProviderFacade::shouldReceive('isBanned')
            ->once()
            ->with(1)
            ->andReturn(true);

        $user = new User(['id'=> 1, 'email' => 'fatemeh@gmail.com']);
        UserProviderFacade::shouldReceive('getUserByEmail')
            ->andReturn(nullable($user));
        // mock
        TokenGeneratorFacade::shouldReceive('generateToken')->never();
        TokenStoreFacade::shouldReceive('saveToken')->never();
        TokenSenderFacade::shouldReceive('send')->never();

        $respo = $this->get('/api/two-factor-auth/request-token?email=fatemeh@gmail.com');
        $respo->assertStatus(400);
        $respo->assertJson(['error' => 'You are blocked']);
    }

    public function test_email_does_not_exist()
    {
        UserProviderFacade::shouldReceive('getUserByEmail')
            ->once()
            ->with('iman@gmail.com')
            ->andReturn(nullable(null));
        // mock
        UserProviderFacade::shouldReceive('isBanned')->never();
        TokenGeneratorFacade::shouldReceive('generateToken')->never();
        TokenStoreFacade::shouldReceive('saveToken')->never();
        TokenSenderFacade::shouldReceive('send')->never();
        ResponderFacade::shouldReceive('userNotFound')->once()->andReturn(response('hello'));
        $resp = $this->get('/api/two-factor-auth/request-token?email=fatemeh@gmail.com');
        $resp->assertSee('hello');
    }

    public function test_email_not_valid()
    {
        UserProviderFacade::shouldReceive('getUserByEmail')
            ->never();
        UserProviderFacade::shouldReceive('isBanned')->never();
        TokenGeneratorFacade::shouldReceive('generateToken')->never();
        TokenStoreFacade::shouldReceive('saveToken')->never();
        TokenSenderFacade::shouldReceive('send')->never();
        ResponderFacade::shouldReceive('emailNotValid')->once()
            ->andReturn(response('hello'));
        $resp = $this->get('/api/two-factor-auth/request-token?email=fatemeh_gmail.com');
        $resp->assertSee('hello');
    }

    public function test_user_is_guest()
    {
        AuthFacade::shouldReceive('check')->once()->andReturn(true);
        UserProviderFacade::shouldReceive('getUserByEmail')
            ->never();
        UserProviderFacade::shouldReceive('isBanned')->never();
        TokenGeneratorFacade::shouldReceive('generateToken')->never();
        TokenStoreFacade::shouldReceive('saveToken')->never();
        TokenSenderFacade::shouldReceive('send')->never();
        ResponderFacade::shouldReceive('youShouldBeGuest')->once()
            ->andReturn(response('hello'));
        $resp = $this->get('/api/two-factor-auth/request-token?email=fatemeh@gmail.com');
        $resp->assertSee('hello');
    }
}






