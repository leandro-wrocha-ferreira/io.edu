<?php

namespace tests\unit\usecases\finance;

use app\domain\exceptions\NotFoundException;
use app\domain\finance\CostCenter;
use app\usecases\finance\UpdateCostCenterUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCostCenterRepository;

/**
 * Unit tests for UpdateCostCenterUseCase.
 */
class UpdateCostCenterUseCaseTest extends TestCase
{
	private MockCostCenterRepository $repository;
	private UpdateCostCenterUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->repository = new MockCostCenterRepository();
		$this->use_case = new UpdateCostCenterUseCase($this->repository);
	}

	public function test_execute_updates_cost_center_successfully(): void
	{
		$saved = $this->repository->save(CostCenter::create('Nome Antigo', 'Desc Antiga', true));

		$updated = $this->use_case->execute($saved->get_id(), 'Nome Atualizado', 'Desc Nova', false);

		$this->assertSame('Nome Atualizado', $updated->get_name());
		$this->assertSame('Desc Nova', $updated->get_description());
		$this->assertFalse($updated->is_active());
	}

	public function test_execute_throws_not_found_when_cost_center_not_exists(): void
	{
		$this->expectException(NotFoundException::class);
		$this->expectExceptionMessage('Cost center not found');

		$this->use_case->execute(999, 'Nome');
	}
}
