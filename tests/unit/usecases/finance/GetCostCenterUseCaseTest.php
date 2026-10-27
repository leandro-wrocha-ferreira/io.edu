<?php

namespace tests\unit\usecases\finance;

use app\domain\exceptions\NotFoundException;
use app\domain\finance\CostCenter;
use app\usecases\finance\GetCostCenterUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCostCenterRepository;

/**
 * Unit tests for GetCostCenterUseCase.
 */
class GetCostCenterUseCaseTest extends TestCase
{
	private MockCostCenterRepository $repository;
	private GetCostCenterUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->repository = new MockCostCenterRepository();
		$this->use_case = new GetCostCenterUseCase($this->repository);
	}

	public function test_execute_returns_cost_center_when_found(): void
	{
		$saved = $this->repository->save(CostCenter::create('Centro Principal', null, true));

		$result = $this->use_case->execute($saved->get_id());

		$this->assertSame($saved->get_id(), $result->get_id());
		$this->assertSame('Centro Principal', $result->get_name());
	}

	public function test_execute_throws_not_found_when_not_exists(): void
	{
		$this->expectException(NotFoundException::class);
		$this->expectExceptionMessage('Cost center not found');

		$this->use_case->execute(999);
	}
}
