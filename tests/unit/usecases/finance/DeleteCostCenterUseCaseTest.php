<?php

namespace tests\unit\usecases\finance;

use app\domain\exceptions\ConflictException;
use app\domain\exceptions\NotFoundException;
use app\domain\finance\CostCenter;
use app\usecases\finance\DeleteCostCenterUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCostCenterRepository;

/**
 * Unit tests for DeleteCostCenterUseCase.
 */
class DeleteCostCenterUseCaseTest extends TestCase
{
	private MockCostCenterRepository $repository;
	private DeleteCostCenterUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->repository = new MockCostCenterRepository();
		$this->use_case = new DeleteCostCenterUseCase($this->repository);
	}

	public function test_execute_deletes_cost_center_successfully(): void
	{
		$saved = $this->repository->save(CostCenter::create('Centro Deletavel', null, true));

		$result = $this->use_case->execute($saved->get_id());

		$this->assertTrue($result);
		$this->assertNull($this->repository->find_by_id($saved->get_id()));
	}

	public function test_execute_throws_not_found_when_not_exists(): void
	{
		$this->expectException(NotFoundException::class);
		$this->expectExceptionMessage('Cost center not found');

		$this->use_case->execute(999);
	}

	public function test_execute_throws_conflict_when_has_linked_products(): void
	{
		$saved = $this->repository->save(CostCenter::create('Centro Vinculado', null, true));
		$this->repository->set_has_linked_products($saved->get_id(), true);

		$this->expectException(ConflictException::class);
		$this->expectExceptionMessage('Cannot delete cost center with linked products');

		$this->use_case->execute($saved->get_id());
	}
}
