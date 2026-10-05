<?php

namespace tests\unit\usecases\course;

use app\domain\course\Category;
use app\usecases\course\ListPaginatedCategoriesUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCategoryRepository;

/**
 * Unit tests for ListPaginatedCategoriesUseCase.
 */
class ListPaginatedCategoriesUseCaseTest extends TestCase
{
	private MockCategoryRepository $category_repository;
	private ListPaginatedCategoriesUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->category_repository = new MockCategoryRepository();
		$this->use_case = new ListPaginatedCategoriesUseCase($this->category_repository);
	}

	public function test_execute_pagination(): void
	{
		$this->category_repository->create(Category::create('Frontend', 'frontend'));
		$this->category_repository->create(Category::create('Backend', 'backend'));
		$this->category_repository->create(Category::create('DevOps', 'devops'));

		$result = $this->use_case->execute(0, 2, '', 'name', 'ASC');

		$this->assertSame(3, $result['recordsTotal']);
		$this->assertSame(3, $result['recordsFiltered']);
		$this->assertCount(2, $result['data']);
	}

	public function test_execute_with_search(): void
	{
		$this->category_repository->create(Category::create('Frontend Web', 'frontend-web'));
		$this->category_repository->create(Category::create('Mobile iOS', 'mobile-ios'));

		$result = $this->use_case->execute(0, 10, 'Mobile', 'name', 'ASC');

		$this->assertSame(2, $result['recordsTotal']);
		$this->assertSame(1, $result['recordsFiltered']);
		$this->assertCount(1, $result['data']);
		$this->assertSame('Mobile iOS', $result['data'][0]->get_name());
	}
}
