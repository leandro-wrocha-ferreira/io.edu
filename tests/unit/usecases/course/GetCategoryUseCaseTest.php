<?php

namespace tests\unit\usecases\course;

use app\domain\course\Category;
use app\domain\exceptions\CategoryNotFoundException;
use app\usecases\course\GetCategoryUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCategoryRepository;

/**
 * Unit tests for GetCategoryUseCase.
 */
class GetCategoryUseCaseTest extends TestCase
{
	private MockCategoryRepository $category_repository;
	private GetCategoryUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->category_repository = new MockCategoryRepository();
		$this->use_case = new GetCategoryUseCase($this->category_repository);
	}

	public function test_get_category_success(): void
	{
		$created = $this->category_repository->create(Category::create('Idiomas', 'idiomas'));

		$category = $this->use_case->execute($created->get_id());

		$this->assertSame($created->get_id(), $category->get_id());
		$this->assertSame('Idiomas', $category->get_name());
	}

	public function test_get_non_existing_category_throws_exception(): void
	{
		$this->expectException(CategoryNotFoundException::class);
		$this->use_case->execute(999);
	}
}
