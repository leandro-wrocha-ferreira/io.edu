<?php

namespace tests\unit\usecases\course;

use app\domain\course\Course;
use app\domain\exceptions\CourseNotFoundException;
use app\usecases\course\ArchiveCourseUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseRepository;

/**
 * Unit tests for ArchiveCourseUseCase.
 */
class ArchiveCourseUseCaseTest extends TestCase
{
	private MockCourseRepository $course_repository;
	private ArchiveCourseUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->course_repository = new MockCourseRepository();
		$this->use_case = new ArchiveCourseUseCase($this->course_repository);
	}

	public function test_archive_course_success(): void
	{
		$course = $this->course_repository->create(Course::create(
			1,
			'Curso Ativo',
			'curso-ativo'
		));

		$archived = $this->use_case->execute($course->get_id());

		$this->assertTrue($archived->is_archived());
		$this->assertFalse($archived->is_active());
	}

	public function test_archive_non_existing_course_throws_exception(): void
	{
		$this->expectException(CourseNotFoundException::class);
		$this->use_case->execute(999);
	}
}
