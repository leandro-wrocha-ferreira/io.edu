<?php

namespace tests\unit\usecases\category;

use app\domain\course\Category;
use app\domain\course\constants\CategoryStatus;
use app\domain\exceptions\ConflictException;
use app\domain\exceptions\NotFoundException;
use app\usecases\category\UpdateCategoryUseCase;
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
		$this->category_repository = new MockCategoryRepository();
		$this->use_case = new UpdateCategoryUseCase($this->category_repository);
	}

	public function test_update_category_success(): void
	{
		$created = $this->category_repository->create(Category::create('Negócios', 'negocios'));

		$updated = $this->use_case->execute(
			$created->get_id(),
			'Negócios & Gestão',
			'negocios-gestao',
			CategoryStatus::INACTIVE
		);

		$this->assertSame('Negócios & Gestão', $updated->get_name());
		$this->assertSame('negocios-gestao', $updated->get_slug());
		$this->assertSame(CategoryStatus::INACTIVE, $updated->get_status());
		$this->assertFalse($updated->is_active());
	}

	public function test_update_category_not_found_throws_not_found_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999, 'Não Existe', 'nao-existe');
	}

	public function test_update_category_duplicate_slug_throws_conflict_exception(): void
	{
		$this->category_repository->create(Category::create('Cat 1', 'slug-1'));
		$cat2 = $this->category_repository->create(Category::create('Cat 2', 'slug-2'));

		$this->expectException(ConflictException::class);
		$this->use_case->execute($cat2->get_id(), 'Cat 2 Modificado', 'slug-1');
	}
}
