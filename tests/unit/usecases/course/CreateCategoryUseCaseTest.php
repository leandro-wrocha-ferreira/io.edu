<?php

namespace tests\unit\usecases\course;

use app\domain\exceptions\DuplicateSlugException;
use app\usecases\course\CreateCategoryUseCase;
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
		parent::setUp();
		$this->category_repository = new MockCategoryRepository();
		$this->use_case = new CreateCategoryUseCase($this->category_repository);
	}

	public function test_create_category_with_explicit_slug_success(): void
	{
		$category = $this->use_case->execute('Tecnologia da Informação', 'tecnologia-ti', 'active');

		$this->assertNotNull($category->get_id());
		$this->assertSame('Tecnologia da Informação', $category->get_name());
		$this->assertSame('tecnologia-ti', $category->get_slug());
		$this->assertSame('active', $category->get_status());
		$this->assertTrue($category->is_active());
	}

	public function test_create_category_with_auto_generated_slug_success(): void
	{
		$category = $this->use_case->execute('Gestão & Negócios');

		$this->assertSame('gestao-negocios', $category->get_slug());
	}

	public function test_create_category_with_duplicate_slug_throws_exception(): void
	{
		$this->use_case->execute('Programação', 'programacao');

		$this->expectException(DuplicateSlugException::class);
		$this->use_case->execute('Programação Web', 'programacao');
	}
}
