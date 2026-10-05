<?php

namespace tests\unit\usecases\course;

use app\domain\course\Course;
use app\usecases\course\ListCoursesUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseRepository;

/**
 * Unit tests for ListCoursesUseCase.
 */
class ListCoursesUseCaseTest extends TestCase
{
	private MockCourseRepository $course_repository;
	private ListCoursesUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->course_repository = new MockCourseRepository();
		$this->use_case = new ListCoursesUseCase($this->course_repository);
	}

	public function test_list_all_courses(): void
	{
		$this->course_repository->create(Course::create(1, 'Curso 1', 'curso-1'));
		$this->course_repository->create(Course::create(1, 'Curso 2', 'curso-2'));

		$courses = $this->use_case->execute();

		$this->assertCount(2, $courses);
	}
}
