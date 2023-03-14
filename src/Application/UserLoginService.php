<?php

namespace UserLoginService\Application;

use Exception;
use MongoDB\Driver\Session;
use UserLoginService\Domain\User;
use UserLoginService\Infrastructure\FacebookSessionManager;

class UserLoginService
{
    private array $loggedUsers = [];
    private SessionManager $sessionManager;

    /**
     * @param SessionManager $sessionManager
     */
    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }


    /**
     * @throws Exception
     */
    public function manualLogin(User $user): void
    {
        if(in_array($user, $this->loggedUsers)){
            throw new Exception(("User already logged in"));
        }

        $this->loggedUsers[] = $user;
    }

    public function login(string $userName, string $password): string
    {
        if($this->sessionManager->login($userName, $password)){
            $user = new User($userName);
            $this->loggedUsers[] = $user;
            return "Login correcto";    // HACER CONSTANTES DE ESTOS 2 STRINGS.
        }

        return "Login incorrecto";
    }

    public function getExternalSessions(): int
    {
        return $this->sessionManager->getSessions();
    }

    public function getLoggedUsers(): array
    {
        return $this->loggedUsers;
    }
}