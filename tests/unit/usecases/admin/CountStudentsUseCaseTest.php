<?php

namespace tests\unit\usecases\admin;

use app\domain\identity\Email;
use app\domain\identity\User;
use app\usecases\admin\CountStudentsUseCase;
use tests\unit\mocks\repositories\MockUserRepository;

/**
 * Test suite for CountStudentsUseCase.
 */
class CountStudentsUseCaseTest extends \PHPUnit\Framework\TestCase
{
	/** @var MockUserRepository */
	private $mock_user_repository;

	/**
	 * Set up test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		$this->mock_user_repository = new MockUserRepository();
	}

	/**
	 * Test counting active students returns correct number.
	 *
	 * @return void
	 */
	public function test_count_students_returns_active_student_count()
	{
		$student_one = User::create('Student One', new Email('student1@example.com'), 'password123');
		$student_one->set_role('student');

		$student_two = User::create('Student Two', new Email('student2@example.com'), 'password123');
		$student_two->set_role('student');

		$admin_user = User::create('Admin User', new Email('admin@example.com'), 'password123');
		$admin_user->set_role('admin');

		$this->mock_user_repository->save($student_one);
		$this->mock_user_repository->save($student_two);
		$this->mock_user_repository->save($admin_user);

		$use_case = new CountStudentsUseCase($this->mock_user_repository);
		$total_students = $use_case->execute();

		$this->assertEquals(2, $total_students);
	}

	/**
	 * Test counting students when there are none returns zero.
	 *
	 * @return void
	 */
	public function test_count_students_when_empty_returns_zero()
	{
		$use_case = new CountStudentsUseCase($this->mock_user_repository);
		$total_students = $use_case->execute();

		$this->assertEquals(0, $total_students);
	}
}
