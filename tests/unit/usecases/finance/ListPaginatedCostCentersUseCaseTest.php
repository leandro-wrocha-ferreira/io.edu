<?php

namespace tests\unit\usecases\finance;

use app\domain\finance\CostCenter;
use app\usecases\finance\ListPaginatedCostCentersUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCostCenterRepository;

/**
 * Unit tests for ListPaginatedCostCentersUseCase.
 */
class ListPaginatedCostCentersUseCaseTest extends TestCase
{
	private MockCostCenterRepository $repository;
	private ListPaginatedCostCentersUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->repository = new MockCostCenterRepository();
		$this->use_case = new ListPaginatedCostCentersUseCase($this->repository);
	}

	public function test_execute_returns_paginated_and_filtered_cost_centers(): void
	{
		$this->repository->save(CostCenter::create('Cursos Livres', 'Descrição', true));
		$this->repository->save(CostCenter::create('Pós-Graduação', null, true));
		$this->repository->save(CostCenter::create('Extensão Inativa', null, false));

		$result = $this->use_case->execute(0, 10, 'Livres', 'name', 'ASC');

		$this->assertCount(1, $result['data']);
		$this->assertSame(1, $result['recordsFiltered']);
		$this->assertSame(3, $result['recordsTotal']);
		$this->assertSame('Cursos Livres', $result['data'][0]->get_name());
	}

	public function test_execute_with_status_filter(): void
	{
		$this->repository->save(CostCenter::create('Ativo 1', null, true));
		$this->repository->save(CostCenter::create('Inativo 1', null, false));

		$result = $this->use_case->execute(0, 10, '', 'name', 'ASC', 1);

		$this->assertCount(1, $result['data']);
		$this->assertSame('Ativo 1', $result['data'][0]->get_name());
	}
}
