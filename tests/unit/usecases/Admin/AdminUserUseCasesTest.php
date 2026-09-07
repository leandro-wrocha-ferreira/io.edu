<?php

use app\domain\exceptions\NotFoundException;
use app\domain\exceptions\ValidationException;
use app\domain\identity\Email;
use app\domain\identity\User;
use app\domain\identity\UserRepositoryInterface;
use app\usecases\admin\ActivateUserUseCase;
use app\usecases\admin\CountStudentsUseCase;
use app\usecases\admin\CreateUserUseCase;
use app\usecases\admin\DeleteUserUseCase;
use app\usecases\admin\DisableUserUseCase;
use app\usecases\admin\GetUserUseCase;
use app\usecases\admin\ListPaginatedUsersUseCase;
use app\usecases\admin\ListUsersUseCase;
use app\usecases\admin\UpdateUserUseCase;

/**
 * Mock UserRepository for Admin UseCases testing.
 */
class AdminMockUserRepository implements UserRepositoryInterface
{
	public array $users = [];

	public function find_by_id($id): ?User
	{
		$id = (int) $id;
		foreach ($this->users as $u) {
			if ($u->get_id() === $id) {
				return $u;
			}
		}
		return null;
	}

	public function find_by_email(Email $email): ?User
	{
		foreach ($this->users as $u) {
			if ($u->get_email()->equals($email)) {
				return $u;
			}
		}
		return null;
	}

	public function save(User $user): void
	{
		if ($user->get_id() === null) {
			$user->set_id(count($this->users) + 1);
			$this->users[] = $user;
		} else {
			foreach ($this->users as $k => $u) {
				if ($u->get_id() === $user->get_id()) {
					$this->users[$k] = $user;
					return;
				}
			}
			$this->users[] = $user;
		}
	}

	public function delete(array $where): bool
	{
		$id = $where['id'] ?? null;
		if ($id !== null) {
			foreach ($this->users as $u) {
				if ($u->get_id() === (int) $id) {
					$u->delete();
				}
			}
		}
		return true;
	}

	public function find_all(): array
	{
		return array_filter($this->users, fn($u) => !$u->is_deleted());
	}

	public function count_by_role(string $role): int
	{
		$cnt = 0;
		foreach ($this->users as $u) {
			if ($u->has_role($role) && !$u->is_deleted()) {
				$cnt++;
			}
		}
		return $cnt;
	}

	public function find_paginated(int $start, int $length, string $search, string $order_col, string $order_dir): array
	{
		$data = array_map(fn($u) => [
			'id' => $u->get_id(),
			'name' => $u->get_name(),
			'email' => (string) $u->get_email(),
		], $this->users);

		return [
			'data' => array_slice($data, $start, $length),
			'recordsFiltered' => count($this->users),
		];
	}

	public function count_all(): int
	{
		return count($this->users);
	}
}

class AdminMockRoleRepository implements \app\domain\admin\role\RoleRepositoryInterface
{
	public function find_by_id($id): ?\app\domain\admin\role\Role { return null; }
	public function find_all(): array { return []; }
	public function save(\app\domain\admin\role\Role $role): void {}
	public function delete(array $where): bool { return true; }
	public function find_paginated(int $start, int $length, string $search, string $order_col, string $order_dir): array { return []; }
	public function count_all(): int { return 0; }
}

/**
 * Test suite covering all Admin User Use Cases.
 */
class AdminUserUseCasesTest extends \PHPUnit\Framework\TestCase
{
	private AdminMockUserRepository $repo;
	private AdminMockRoleRepository $roleRepo;

	protected function setUp(): void
	{
		$this->repo = new AdminMockUserRepository();
		$this->roleRepo = new AdminMockRoleRepository();
	}

	public function test_create_user_success(): void
	{
		$useCase = new CreateUserUseCase($this->repo, $this->roleRepo);
		$user = $useCase->execute('Novo Aluno', 'aluno@teste.com', 'senha123', [2]);

		$this->assertEquals('Novo Aluno', $user->get_name());
		$this->assertEquals('aluno@teste.com', (string) $user->get_email());
		$this->assertEquals([2], $user->get_role_ids());
		$this->assertNotNull($this->repo->find_by_email(new Email('aluno@teste.com')));
	}

	public function test_create_user_duplicate_email_throws_exception(): void
	{
		$existing = User::create('Existente', new Email('duplicado@teste.com'), '123');
		$this->repo->save($existing);

		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('E-mail já está em uso');

		$useCase = new CreateUserUseCase($this->repo, $this->roleRepo);
		$useCase->execute('Outro', 'duplicado@teste.com', 'senha123');
	}

	public function test_update_user_success(): void
	{
		$user = User::create('Antigo', new Email('antigo@teste.com'), '123');
		$this->repo->save($user);

		$useCase = new UpdateUserUseCase($this->repo);
		$updated = $useCase->execute($user->get_id(), 'Atualizado', 'novo@teste.com', [3]);

		$this->assertEquals('Atualizado', $updated->get_name());
		$this->assertEquals('novo@teste.com', (string) $updated->get_email());
		$this->assertEquals([3], $updated->get_role_ids());
	}

	public function test_update_user_not_found_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->expectExceptionMessage('Usuário não encontrado');

		$useCase = new UpdateUserUseCase($this->repo);
		$useCase->execute(999, 'Nome', 'email@teste.com');
	}

	public function test_update_user_duplicate_email_throws_exception(): void
	{
		$user1 = User::create('User 1', new Email('user1@teste.com'), '123');
		$user2 = User::create('User 2', new Email('user2@teste.com'), '123');
		$this->repo->save($user1);
		$this->repo->save($user2);

		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('E-mail já está em uso');

		$useCase = new UpdateUserUseCase($this->repo);
		$useCase->execute($user2->get_id(), 'User 2', 'user1@teste.com');
	}

	public function test_delete_user_success(): void
	{
		$user = User::create('Deletar', new Email('delete@teste.com'), '123');
		$this->repo->save($user);

		$useCase = new DeleteUserUseCase($this->repo);
		$useCase->execute($user->get_id());

		$found = $this->repo->find_by_id($user->get_id());
		$this->assertTrue($found->is_deleted());
	}

	public function test_delete_user_not_found_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$useCase = new DeleteUserUseCase($this->repo);
		$useCase->execute(999);
	}

	public function test_get_user_success(): void
	{
		$user = User::create('Buscar', new Email('buscar@teste.com'), '123');
		$this->repo->save($user);

		$useCase = new GetUserUseCase($this->repo);
		$found = $useCase->execute($user->get_id());

		$this->assertEquals('Buscar', $found->get_name());
	}

	public function test_get_user_not_found_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$useCase = new GetUserUseCase($this->repo);
		$useCase->execute(999);
	}

	public function test_activate_and_disable_user(): void
	{
		$user = User::create('Ativo', new Email('ativo@teste.com'), '123');
		$this->repo->save($user);

		$disableUseCase = new DisableUserUseCase($this->repo);
		$disableUseCase->execute($user->get_id());
		$this->assertFalse($user->is_active());

		$activateUseCase = new ActivateUserUseCase($this->repo);
		$activateUseCase->execute($user->get_id());
		$this->assertTrue($user->is_active());
	}

	public function test_list_users_and_pagination(): void
	{
		$u1 = User::create('U1', new Email('u1@test.com'), '123');
		$u2 = User::create('U2', new Email('u2@test.com'), '123');
		$this->repo->save($u1);
		$this->repo->save($u2);

		$listUseCase = new ListUsersUseCase($this->repo);
		$all = $listUseCase->execute();
		$this->assertCount(2, $all);

		$paginatedUseCase = new ListPaginatedUsersUseCase($this->repo);
		$result = $paginatedUseCase->execute(0, 10, '', 'name', 'ASC');
		$this->assertEquals(2, $result['recordsTotal']);
		$this->assertCount(2, $result['data']);
	}

	public function test_count_students(): void
	{
		$u1 = User::create('Student', new Email('s1@test.com'), '123');
		$u1->set_role('student');
		$this->repo->save($u1);

		$countUseCase = new CountStudentsUseCase($this->repo);
		$total = $countUseCase->execute();
		$this->assertEquals(1, $total);
	}
}
