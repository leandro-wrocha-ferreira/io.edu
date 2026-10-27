<?php

namespace tests\unit\usecases\commercial;

use app\domain\commercial\ProductPriceVersion;
use app\domain\commercial\value_objects\Price;
use app\usecases\commercial\ListProductPriceVersionsUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockProductPriceVersionRepository;

/**
 * Unit tests for ListProductPriceVersionsUseCase.
 */
class ListProductPriceVersionsUseCaseTest extends TestCase
{
	private MockProductPriceVersionRepository $price_version_repository;
	private ListProductPriceVersionsUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->price_version_repository = new MockProductPriceVersionRepository();
		$this->use_case = new ListProductPriceVersionsUseCase($this->price_version_repository);
	}

	public function test_list_versions_returns_versions_descending(): void
	{
		$this->price_version_repository->save(ProductPriceVersion::create(
			10,
			1,
			null,
			Price::from_cents(5000),
			['price' => 5000]
		));

		$this->price_version_repository->save(ProductPriceVersion::create(
			10,
			2,
			Price::from_cents(5000),
			Price::from_cents(8000),
			['price' => 8000]
		));

		$results = $this->use_case->execute(10);

		$this->assertCount(2, $results);
		$this->assertSame(2, $results[0]->get_version_number());
		$this->assertSame(8000, $results[0]->get_new_price()->get_cents());
		$this->assertSame(1, $results[1]->get_version_number());
		$this->assertSame(5000, $results[1]->get_new_price()->get_cents());
	}

	public function test_list_versions_returns_empty_array_when_no_versions(): void
	{
		$results = $this->use_case->execute(999);
		$this->assertSame([], $results);
	}
}
