<?php

namespace tests\unit\usecases\finance;

use app\domain\finance\CostCenter;
use app\usecases\finance\ListActiveCostCentersUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCostCenterRepository;

/**
 * Unit tests for ListActiveCostCentersUseCase.
 */
class ListActiveCostCentersUseCaseTest extends TestCase
{
	private MockCostCenterRepository $repository;
	private ListActiveCostCentersUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->repository = new MockCostCenterRepository();
		$this->use_case = new ListActiveCostCentersUseCase($this->repository);
	}

	public function test_list_active_returns_only_active_cost_centers(): void
	{
		$this->repository->save(CostCenter::create('Centro Ativo', null, true));
		$this->repository->save(CostCenter::create('Centro Inativo', null, false));

		$results = $this->use_case->execute();

		$this->assertCount(1, $results);
		$this->assertSame('Centro Ativo', $results[0]->get_name());
	}

	public function test_list_active_returns_empty_when_none_active(): void
	{
		$this->repository->save(CostCenter::create('Centro Inativo', null, false));

		$results = $this->use_case->execute();
		$this->assertSame([], $results);
	}
}
