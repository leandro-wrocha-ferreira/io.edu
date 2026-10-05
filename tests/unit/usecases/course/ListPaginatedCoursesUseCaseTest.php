<?php

namespace tests\unit\usecases\course;

use app\domain\course\Course;
use app\domain\course\value_objects\CourseStatus;
use app\usecases\course\ListPaginatedCoursesUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseRepository;

/**
 * Unit tests for ListPaginatedCoursesUseCase.
 */
class ListPaginatedCoursesUseCaseTest extends TestCase
{
	private MockCourseRepository $course_repository;
	private ListPaginatedCoursesUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->course_repository = new MockCourseRepository();
		$this->use_case = new ListPaginatedCoursesUseCase($this->course_repository);
	}

	public function test_execute_pagination(): void
	{
		$this->course_repository->create(Course::create(1, 'Curso A', 'curso-a'));
		$this->course_repository->create(Course::create(1, 'Curso B', 'curso-b'));
		$this->course_repository->create(Course::create(1, 'Curso C', 'curso-c'));

		$result = $this->use_case->execute(0, 2, '', 'title', 'ASC');

		$this->assertSame(3, $result['recordsTotal']);
		$this->assertSame(3, $result['recordsFiltered']);
		$this->assertCount(2, $result['data']);
	}

	public function test_execute_with_search_and_filters(): void
	{
		$this->course_repository->create(Course::create(1, 'Node.js Backend', 'nodejs-backend', CourseStatus::ACTIVE));
		$this->course_repository->create(Course::create(2, 'React Frontend', 'react-frontend', CourseStatus::DRAFT));
		$this->course_repository->create(Course::create(1, 'Python AI', 'python-ai', CourseStatus::ACTIVE));

		// Filter by category_id 1
		$res_cat = $this->use_case->execute(0, 10, '', 'title', 'ASC', 1);
		$this->assertSame(3, $res_cat['recordsTotal']);
		$this->assertSame(2, $res_cat['recordsFiltered']);
		$this->assertCount(2, $res_cat['data']);

		// Filter by status 'draft'
		$res_status = $this->use_case->execute(0, 10, '', 'title', 'ASC', null, CourseStatus::DRAFT);
		$this->assertSame(1, $res_status['recordsFiltered']);
		$this->assertSame('React Frontend', $res_status['data'][0]->get_title());

		// Search term
		$res_search = $this->use_case->execute(0, 10, 'Python', 'title', 'ASC');
		$this->assertSame(1, $res_search['recordsFiltered']);
		$this->assertSame('Python AI', $res_search['data'][0]->get_title());
	}
}
