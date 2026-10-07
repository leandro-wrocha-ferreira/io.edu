<?php

namespace tests\unit\usecases\category;

use app\domain\course\Category;
use app\domain\course\Course;
use app\domain\exceptions\ConflictException;
use app\domain\exceptions\NotFoundException;
use app\usecases\category\DeleteCategoryUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCategoryRepository;
use tests\unit\mocks\repositories\MockCourseRepository;

/**
 * Unit tests for DeleteCategoryUseCase.
 */
class DeleteCategoryUseCaseTest extends TestCase
{
	private MockCategoryRepository $category_repository;
	private MockCourseRepository $course_repository;
	private DeleteCategoryUseCase $use_case;

	protected function setUp(): void
	{
		$this->category_repository = new MockCategoryRepository();
		$this->course_repository = new MockCourseRepository();
		$this->use_case = new DeleteCategoryUseCase($this->category_repository, $this->course_repository);
	}

	public function test_delete_category_success(): void
	{
		$category = $this->category_repository->create(Category::create('Idiomas', 'idiomas'));

		$deleted = $this->use_case->execute($category->get_id());

		$this->assertTrue($deleted->is_deleted());
		$this->assertNull($this->category_repository->find_by_id($category->get_id()));
	}

	public function test_delete_category_not_found_throws_not_found_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999);
	}

	public function test_delete_category_with_linked_courses_throws_conflict_exception(): void
	{
		$category = $this->category_repository->create(Category::create('Programação', 'programacao'));

		$this->course_repository->create(Course::create(
			$category->get_id(),
			'Curso com categoria',
			'curso-com-categoria'
		));

		$this->expectException(ConflictException::class);
		$this->use_case->execute($category->get_id());
	}
}
