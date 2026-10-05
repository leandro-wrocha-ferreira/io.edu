<?php

namespace tests\unit\usecases\course;

use app\domain\course\Category;
use app\domain\exceptions\CategoryNotFoundException;
use app\domain\exceptions\DuplicateSlugException;
use app\usecases\course\UpdateCategoryUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCategoryRepository;

/**
 * Unit tests for UpdateCategoryUseCase.
 */
class UpdateCategoryUseCaseTest extends TestCase
{
	private MockCategoryRepository $category_repository;
	private UpdateCategoryUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->category_repository = new MockCategoryRepository();
		$this->use_case = new UpdateCategoryUseCase($this->category_repository);
	}

	public function test_update_category_success(): void
	{
		$category = $this->category_repository->create(Category::create('Design', 'design'));

		$updated = $this->use_case->execute($category->get_id(), 'Design Gráfico', 'design-grafico', 'inactive');

		$this->assertSame('Design Gráfico', $updated->get_name());
		$this->assertSame('design-grafico', $updated->get_slug());
		$this->assertSame('inactive', $updated->get_status());
	}

	public function test_update_category_keeping_same_slug_success(): void
	{
		$category = $this->category_repository->create(Category::create('Design', 'design'));

		$updated = $this->use_case->execute($category->get_id(), 'Novo Design', 'design');

		$this->assertSame('Novo Design', $updated->get_name());
		$this->assertSame('design', $updated->get_slug());
	}

	public function test_update_non_existing_category_throws_exception(): void
	{
		$this->expectException(CategoryNotFoundException::class);
		$this->use_case->execute(999, 'Qualquer');
	}

	public function test_update_with_slug_taken_by_another_category_throws_exception(): void
	{
		$this->category_repository->create(Category::create('Categoria 1', 'slug-1'));
		$category2 = $this->category_repository->create(Category::create('Categoria 2', 'slug-2'));

		$this->expectException(DuplicateSlugException::class);
		$this->use_case->execute($category2->get_id(), 'Categoria 2 Modificada', 'slug-1');
	}
}
