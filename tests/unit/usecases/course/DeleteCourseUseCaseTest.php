<?php

namespace tests\unit\usecases\course;

use app\domain\course\Course;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\DeleteCourseUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseRepository;

/**
 * Unit tests for DeleteCourseUseCase.
 */
class DeleteCourseUseCaseTest extends TestCase
{
	private MockCourseRepository $course_repository;
	private DeleteCourseUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->course_repository = new MockCourseRepository();
		$this->use_case = new DeleteCourseUseCase($this->course_repository);
	}

	public function test_delete_course_success(): void
	{
		$course = $this->course_repository->create(Course::create(
			1,
			'Curso Para Excluir',
			'curso-excluir'
		));

		$deleted = $this->use_case->execute($course->get_id());

		$this->assertTrue($deleted->is_deleted());
		$this->assertNull($this->course_repository->find_by_id($course->get_id()));
	}

	public function test_delete_non_existing_course_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999);
	}
}
