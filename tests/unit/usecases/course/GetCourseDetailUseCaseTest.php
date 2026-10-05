<?php

namespace tests\unit\usecases\course;

use app\domain\course\Course;
use app\domain\exceptions\CourseNotFoundException;
use app\usecases\course\GetCourseDetailUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseRepository;

/**
 * Unit tests for GetCourseDetailUseCase.
 */
class GetCourseDetailUseCaseTest extends TestCase
{
	private MockCourseRepository $course_repository;
	private GetCourseDetailUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->course_repository = new MockCourseRepository();
		$this->use_case = new GetCourseDetailUseCase($this->course_repository);
	}

	public function test_get_course_detail_success(): void
	{
		$created = $this->course_repository->create(Course::create(
			1,
			'Curso Detalhado',
			'curso-detalhado'
		));

		$course = $this->use_case->execute($created->get_id());

		$this->assertSame($created->get_id(), $course->get_id());
		$this->assertSame('Curso Detalhado', $course->get_title());
	}

	public function test_get_non_existing_course_detail_throws_exception(): void
	{
		$this->expectException(CourseNotFoundException::class);
		$this->use_case->execute(999);
	}
}
