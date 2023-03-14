<?php

declare(strict_types=1);

namespace UserLoginService\Tests\Application;

use Exception;
use Mockery;
use PHPUnit\Framework\TestCase;
use UserLoginService\Application\SessionManager;
use UserLoginService\Application\UserLoginService;
use UserLoginService\Domain\User;
// use UserLoginService\Tests\Doubles\FakeSessionManager;
// use UserLoginService\Tests\Doubles\SessionManagerDummy;
// use UserLoginService\Tests\Doubles\SessionManagerStub;

final class UserLoginServiceTest extends TestCase
{

    private SessionManager $sessionManager;
    private UserLoginService $userLoginService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sessionManager = Mockery::mock(SessionManager::class);
        $this->userLoginService = new UserLoginService($this->sessionManager);
    }

    /**
     * @test
     */
    public function exceptionThrownWhileManualLoginIfUserAlreadyLogged()
    {
        // $userLoginService = new UserLoginService(new SessionManagerDummy());
        $user = new User("username");

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("User already logged in");

        $this->userLoginService->manualLogin($user);
        $this->userLoginService->manualLogin($user);
    }

    /**
     * @test
     * @throws Exception
     */
    public function userIsManuallyLoggedIn()
    {
        // $userLoginService = new UserLoginService(new SessionManagerDummy());
        $user = new User("username");
        $expectedLoggedUsers = [$user];

        $this->userLoginService->manualLogin($user);

        $this->assertEquals($expectedLoggedUsers, $this->userLoginService->getLoggedUsers());
    }

    /**
     * @test
     */
    public function returnsNumberOfActiveSesssions()
    {
        // $userLoginService = new UserLoginService(new SessionManagerStub());
        $this->sessionManager->allows()->getSessions()->andReturnTrue();

        $this->assertEquals(1, $this->userLoginService->getExternalSessions());
    }

    /**
     * @test
     */
    public function userNotLoggedInExternalApi()
    {
        // $userLoginService = new UserLoginService(new FakeSessionManager());

        $this->sessionManager->allows()->login("wrong_username", "wrong_password")->andReturnFalse();

        $loginStatus = $this->userLoginService->login("wrong_username", "wrong_password");

        $this->assertEquals("Login incorrecto", $loginStatus);
    }

    /**
     * @test
     */
    public function userLoggedInExternalApi()
    {
        // $userLoginService = new UserLoginService(new FakeSessionManager());

        $expectedUser = new User("username");

        $this->sessionManager->allows()->login("username", "password")->andReturnTrue();

        $loginStatus = $this->userLoginService->login("username", "password");

        $this->assertEquals("Login correcto", $loginStatus);
        $this->assertEquals($expectedUser, $this->userLoginService->getLoggedUsers()[0]);
    }

    /**
     * @test
     */
    public function userNotLoggedOutFromLocalAndExternalSessions()
    {
        // $userLoginService = new UserLoginService(new FakeSessionManager());
        $sessionManager = Mockery::spy(SessionManager::class);
        $userLoginService = new UserLoginService($this->sessionManager);
        $user = new User("username");

        $logoutStatus = $userLoginService->logout($user);

        $sessionManager->shouldNotHaveReceived()->logout($user->getUsername());
        $this->assertEquals("User not found", $logoutStatus);
    }

    /**
     * @test
     */
    public function userLoggedOutFromLocalAndExternalSessions()
    {
        // $userLoginService = new UserLoginService(new FakeSessionManager());

        $sessionManager = Mockery::spy(SessionManager::class);
        $userLoginService = new UserLoginService($sessionManager);
        $user = new User("username");
        $userLoginService->manualLogin($user);

        $logoutStatus = $userLoginService->logout($user);

        $sessionManager->shouldHaveReceived()->logout($user->getUserName());
        $this->assertEquals("Ok", $logoutStatus);
        // $this->assertEquals($expectedUser, $this->userLoginService->getLoggedUsers()[0]);
    }
}
