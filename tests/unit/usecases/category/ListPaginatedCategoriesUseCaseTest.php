<?php

namespace tests\unit\usecases\category;

use app\domain\course\Category;
use app\usecases\category\ListPaginatedCategoriesUseCase;
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
		$this->category_repository = new MockCategoryRepository();
		$this->use_case = new ListPaginatedCategoriesUseCase($this->category_repository);
	}

	public function test_list_paginated_categories(): void
	{
		$this->category_repository->create(Category::create('Alpha', 'alpha'));
		$this->category_repository->create(Category::create('Beta', 'beta'));
		$this->category_repository->create(Category::create('Gamma', 'gamma'));

		$result = $this->use_case->execute(0, 2, '', 'name', 'ASC');

		$this->assertCount(2, $result['data']);
		$this->assertSame(3, $result['recordsFiltered']);
		$this->assertSame(3, $result['recordsTotal']);
	}

	public function test_list_paginated_with_search(): void
	{
		$this->category_repository->create(Category::create('Tecnologia Web', 'tecnologia-web'));
		$this->category_repository->create(Category::create('Design UI', 'design-ui'));

		$result = $this->use_case->execute(0, 10, 'Tecnologia', 'name', 'ASC');

		$this->assertCount(1, $result['data']);
		$this->assertSame(1, $result['recordsFiltered']);
		$this->assertSame('Tecnologia Web', $result['data'][0]->get_name());
	}
}
