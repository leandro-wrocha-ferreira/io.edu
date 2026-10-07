<?php

namespace tests\unit\usecases\category;

use app\domain\course\Category;
use app\domain\course\constants\CategoryStatus;
use app\usecases\category\ListCategoriesUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCategoryRepository;

/**
 * Unit tests for ListCategoriesUseCase.
 */
class ListCategoriesUseCaseTest extends TestCase
{
	private MockCategoryRepository $category_repository;
	private ListCategoriesUseCase $use_case;

	protected function setUp(): void
	{
		$this->category_repository = new MockCategoryRepository();
		$this->use_case = new ListCategoriesUseCase($this->category_repository);
	}

	public function test_list_all_categories(): void
	{
		$this->category_repository->create(Category::create('Cat 1', 'cat-1', CategoryStatus::ACTIVE));
		$this->category_repository->create(Category::create('Cat 2', 'cat-2', CategoryStatus::INACTIVE));

		$result = $this->use_case->execute(false);

		$this->assertCount(2, $result);
	}

	public function test_list_only_active_categories(): void
	{
		$this->category_repository->create(Category::create('Cat 1', 'cat-1', CategoryStatus::ACTIVE));
		$this->category_repository->create(Category::create('Cat 2', 'cat-2', CategoryStatus::INACTIVE));

		$result = $this->use_case->execute(true);

		$this->assertCount(1, $result);
		$this->assertSame('Cat 1', $result[0]->get_name());
	}
}
