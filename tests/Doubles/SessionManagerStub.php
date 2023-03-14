<?php

namespace UserLoginService\Tests\Doubles;

use UserLoginService\Application\SessionManager;

class SessionManagerStub implements SessionManager
{
    public function getSessions(): int
    {
        return 3;
    }

    public function login(string $userName, string $password): bool
    {
        return false;
    }
}