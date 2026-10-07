<?php

namespace tests\unit\usecases\category;

use app\domain\course\constants\CategoryStatus;
use app\domain\exceptions\ConflictException;
use app\usecases\category\CreateCategoryUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCategoryRepository;

/**
 * Unit tests for CreateCategoryUseCase.
 */
class CreateCategoryUseCaseTest extends TestCase
{
	private MockCategoryRepository $category_repository;
	private CreateCategoryUseCase $use_case;

	protected function setUp(): void
	{
		$this->category_repository = new MockCategoryRepository();
		$this->use_case = new CreateCategoryUseCase($this->category_repository);
	}

	public function test_create_category_success(): void
	{
		$category = $this->use_case->execute('Tecnologia', 'tecnologia', CategoryStatus::ACTIVE);

		$this->assertSame('Tecnologia', $category->get_name());
		$this->assertSame('tecnologia', $category->get_slug());
		$this->assertTrue($category->is_active());
		$this->assertNotNull($category->get_id());
	}

	public function test_create_category_auto_generates_slug_when_null(): void
	{
		$category = $this->use_case->execute('Design Gráfico');

		$this->assertSame('design-grafico', $category->get_slug());
	}

	public function test_create_category_with_duplicate_slug_throws_conflict_exception(): void
	{
		$this->use_case->execute('Tecnologia', 'tecnologia');

		$this->expectException(ConflictException::class);
		$this->use_case->execute('Outra Tecnologia', 'tecnologia');
	}
}
