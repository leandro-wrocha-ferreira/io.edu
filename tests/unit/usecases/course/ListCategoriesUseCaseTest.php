<?php

namespace tests\unit\usecases\course;

use app\domain\course\Category;
use app\usecases\course\ListCategoriesUseCase;
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
		parent::setUp();
		$this->category_repository = new MockCategoryRepository();
		$this->use_case = new ListCategoriesUseCase($this->category_repository);
	}

	public function test_list_all_categories(): void
	{
		$this->category_repository->create(Category::create('Cat 1', 'cat-1', 'active'));
		$this->category_repository->create(Category::create('Cat 2', 'cat-2', 'inactive'));

		$all = $this->use_case->execute(false);
		$this->assertCount(2, $all);
	}

	public function test_list_only_active_categories(): void
	{
		$this->category_repository->create(Category::create('Cat 1', 'cat-1', 'active'));
		$this->category_repository->create(Category::create('Cat 2', 'cat-2', 'inactive'));

		$active = $this->use_case->execute(true);
		$this->assertCount(1, $active);
		$this->assertSame('Cat 1', $active[0]->get_name());
	}
}
