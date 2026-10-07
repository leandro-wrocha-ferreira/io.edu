<?php

namespace tests\unit\usecases\category;

use app\domain\course\Category;
use app\domain\exceptions\NotFoundException;
use app\usecases\category\GetCategoryUseCase;
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
		$this->category_repository = new MockCategoryRepository();
		$this->use_case = new GetCategoryUseCase($this->category_repository);
	}

	public function test_get_category_success(): void
	{
		$created = $this->category_repository->create(Category::create('Marketing', 'marketing'));

		$result = $this->use_case->execute($created->get_id());

		$this->assertSame($created->get_id(), $result->get_id());
		$this->assertSame('Marketing', $result->get_name());
	}

	public function test_get_category_not_found_throws_not_found_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999);
	}
}
