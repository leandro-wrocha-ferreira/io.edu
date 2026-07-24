<?php

namespace app\domain\identity;

class User
{
    private $id;
    private $name;
    private $email;
    private $password;
    private $role;
    private $created_at;
    private $updated_at;
    private $deleted_at;

    public static function create(string $name, Email $email, string $password): self
    {
        $user = new self();
        $user->name = $name;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_BCRYPT);
        $user->created_at = new \DateTime();
        return $user;
    }

    public static function from_database(array $row): self
    {
        $user = new self();
        $user->id = (int) $row['id'];
        $user->name = $row['name'];
        $user->email = new Email($row['email']);
        $user->password = $row['password'];
        $user->role = $row['role'] ?? null;
        $user->created_at = isset($row['created_at']) ? new \DateTime($row['created_at']) : null;
        $user->updated_at = isset($row['updated_at']) ? new \DateTime($row['updated_at']) : null;
        $user->deleted_at = isset($row['deleted_at']) ? new \DateTime($row['deleted_at']) : null;
        return $user;
    }

    public function get_id(): ?int
    {
        return $this->id;
    }

    public function get_name(): string
    {
        return $this->name;
    }

    public function get_email(): Email
    {
        return $this->email;
    }

    public function get_password(): string
    {
        return $this->password;
    }

    public function get_role(): ?string
    {
        return $this->role;
    }

    public function get_created_at(): ?\DateTime
    {
        return $this->created_at;
    }

    public function is_admin(): bool
    {
        return $this->role === 'admin';
    }

    public function is_student(): bool
    {
        return $this->role === 'student';
    }

    public function verify_password(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function change_password(string $new_password): void
    {
        $this->password = password_hash($new_password, PASSWORD_BCRYPT);
        $this->updated_at = new \DateTime();
    }

    public function is_deleted(): bool
    {
        return $this->deleted_at !== null;
    }

    public function delete(): void
    {
        $this->deleted_at = new \DateTime();
    }
}
