<?php

declare(strict_types=1);

namespace UserLoginService\Tests\Application;

use Exception;
use PHPUnit\Framework\TestCase;
use UserLoginService\Application\UserLoginService;
use UserLoginService\Domain\User;
use UserLoginService\Tests\Doubles\FakeSessionManager;
use UserLoginService\Tests\Doubles\SessionManagerDummy;
use UserLoginService\Tests\Doubles\SessionManagerStub;

final class UserLoginServiceTest extends TestCase
{
    /**
     * @test
     */
    public function exceptionThrownWhileManualLoginIfUserAlreadyLogged()
    {
        $userLoginService = new UserLoginService(new SessionManagerDummy());
        $user = new User("username");

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("User already logged in");

        $userLoginService->manualLogin($user);
        $userLoginService->manualLogin($user);
    }

    /**
     * @test
     * @throws Exception
     */
    public function userIsManuallyLoggedIn()
    {
        $userLoginService = new UserLoginService(new SessionManagerDummy());
        $user = new User("username");

        $userLoginService->manualLogin($user);
        $this->assertContains($user, $userLoginService->getLoggedUsers());
    }

    /**
     * @test
     */
    public function returnsNumberOfActiveSesssions()
    {
        $userLoginService = new UserLoginService(new SessionManagerStub());

        $this->assertEquals(3, $userLoginService->getExternalSessions());
    }

    /**
     * @test
     */
    public function userNotLoggedInExternalApi()
    {
        $userLoginService = new UserLoginService(new FakeSessionManager());

        $loginStatus = $userLoginService->login("wrong_username", "wrong_password");

        $this->assertEquals("Login incorrecto", $loginStatus);
    }

    /**
     * @test
     */
    public function userLoggedInExternalApi()
    {
        $userLoginService = new UserLoginService(new FakeSessionManager());

        $expectedUser = new User("username");
        $loginStatus = $userLoginService->login("username", "password");

        $this->assertEquals("Login correcto", $loginStatus);
        $this->assertEquals($expectedUser, $userLoginService->getLoggedUsers()[0]);
    }
}
