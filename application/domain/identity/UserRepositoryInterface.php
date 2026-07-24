<?php

namespace app\domain\identity;

interface UserRepositoryInterface
{
    public function find_by_id(int $id): ?User;

    public function find_by_email(Email $email): ?User;

    public function save(User $user): void;

    public function delete(int $id): void;
}
