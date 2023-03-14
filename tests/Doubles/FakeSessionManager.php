<?php

namespace UserLoginService\Tests\Doubles;

use UserLoginService\Application\SessionManager;

class FakeSessionManager implements SessionManager
{
    public function getSessions(): int
    {
        // TODO: Implement getSessions() method.
    }

    public function login(string $userName, string $password): bool
    {
        if($userName == "username" && $password == "password"){
            return true;
        }
        return false;
    }
}