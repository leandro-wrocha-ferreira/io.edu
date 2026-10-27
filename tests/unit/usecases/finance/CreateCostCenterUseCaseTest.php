<?php

namespace tests\unit\usecases\finance;

use app\domain\exceptions\ValidationException;
use app\usecases\finance\CreateCostCenterUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCostCenterRepository;

/**
 * Unit tests for CreateCostCenterUseCase.
 */
class CreateCostCenterUseCaseTest extends TestCase
{
	private MockCostCenterRepository $repository;
	private CreateCostCenterUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->repository = new MockCostCenterRepository();
		$this->use_case = new CreateCostCenterUseCase($this->repository);
	}

	public function test_execute_creates_cost_center_successfully(): void
	{
		$created = $this->use_case->execute('Centro Novo', 'Descrição detalhada', true);

		$this->assertNotNull($created->get_id());
		$this->assertSame('Centro Novo', $created->get_name());
		$this->assertSame('Descrição detalhada', $created->get_description());
		$this->assertTrue($created->is_active());
	}

	public function test_execute_throws_when_name_is_empty(): void
	{
		$this->expectException(ValidationException::class);
		$this->use_case->execute('');
	}
}
