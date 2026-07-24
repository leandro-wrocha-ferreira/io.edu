<?php

namespace app\usecases\identity;

use app\domain\identity\Email;
use app\domain\identity\User;
use app\Factories\Model_factory;

class AuthenticateUserUseCase
{
    private $user_repository;

    public function __construct($repository = null)
    {
        if ($repository !== null) {
            $this->user_repository = $repository;
        } else {
            $this->user_repository = Model_factory::make('User_model');
        }
    }

    public function execute(string $email, string $password): User
    {
        $user = $this->user_repository->find_by_email(new Email($email));

        if ($user === null) {
            throw new \RuntimeException("Credenciais inválidas");
        }

        if ($user->is_deleted()) {
            throw new \RuntimeException("Conta desativada");
        }

        if (!$user->verify_password($password)) {
            throw new \RuntimeException("Credenciais inválidas");
        }

        return $user;
    }
}
