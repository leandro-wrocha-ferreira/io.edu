<?php

namespace tests\unit\usecases\admin;

use app\domain\identity\Role;
use app\domain\identity\User;
use app\domain\identity\value_objects\Email;
use app\usecases\admin\GetAdminDashboardMetricsUseCase;
use tests\unit\mocks\repositories\MockRoleRepository;
use tests\unit\mocks\repositories\MockUserRepository;

/**
 * Test suite for GetAdminDashboardMetricsUseCase.
 */
class GetAdminDashboardMetricsUseCaseTest extends \PHPUnit\Framework\TestCase
{
	/** @var MockUserRepository */
	private $mock_user_repository;

	/** @var MockRoleRepository */
	private $mock_role_repository;

	/**
	 * Set up test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		$this->mock_user_repository = new MockUserRepository();
		$this->mock_role_repository = new MockRoleRepository();
	}

	/**
	 * Test retrieving dashboard metrics with populated data.
	 *
	 * @return void
	 */
	public function test_get_metrics_returns_correct_counts(): void
	{
		// Create users with different roles
		$student_one = User::create('Student One', new Email('student1@example.com'), 'password123');
		$student_two = User::create('Student Two', new Email('student2@example.com'), 'password123');
		$admin_one = User::create('Admin One', new Email('admin1@example.com'), 'password123');

		$saved_student_one = $this->mock_user_repository->save($student_one);
		$this->mock_user_repository->sync_user_roles($saved_student_one->get_id(), [3]);

		$saved_student_two = $this->mock_user_repository->save($student_two);
		$this->mock_user_repository->sync_user_roles($saved_student_two->get_id(), [3]);

		$saved_admin_one = $this->mock_user_repository->save($admin_one);
		$this->mock_user_repository->sync_user_roles($saved_admin_one->get_id(), [2]);

		// Create roles
		$this->mock_role_repository->save(Role::create('AdminMaster', 'admin-master', 'Master'));
		$this->mock_role_repository->save(Role::create('Admin', 'admin', 'Admin'));
		$this->mock_role_repository->save(Role::create('Student', 'student', 'Student'));

		$use_case = new GetAdminDashboardMetricsUseCase($this->mock_user_repository, $this->mock_role_repository);
		$metrics = $use_case->execute();

		$this->assertIsArray($metrics);
		$this->assertEquals(2, $metrics['total_students']);
		$this->assertEquals(1, $metrics['total_admins']);
		$this->assertEquals(3, $metrics['total_users']);
		$this->assertEquals(3, $metrics['total_roles']);
	}

	/**
	 * Test retrieving dashboard metrics when empty.
	 *
	 * @return void
	 */
	public function test_get_metrics_when_empty_returns_zeroes(): void
	{
		$use_case = new GetAdminDashboardMetricsUseCase($this->mock_user_repository, $this->mock_role_repository);
		$metrics = $use_case->execute();

		$this->assertEquals(0, $metrics['total_students']);
		$this->assertEquals(0, $metrics['total_admins']);
		$this->assertEquals(0, $metrics['total_users']);
		$this->assertEquals(0, $metrics['total_roles']);
	}
}
