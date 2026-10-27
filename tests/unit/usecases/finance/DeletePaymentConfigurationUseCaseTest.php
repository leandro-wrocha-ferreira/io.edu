<?php

namespace tests\unit\usecases\finance;

use app\domain\exceptions\ConflictException;
use app\domain\exceptions\NotFoundException;
use app\domain\finance\PaymentConfiguration;
use app\usecases\finance\DeletePaymentConfigurationUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockPaymentConfigurationRepository;

/**
 * Unit tests for DeletePaymentConfigurationUseCase.
 */
class DeletePaymentConfigurationUseCaseTest extends TestCase
{
	private MockPaymentConfigurationRepository $repository;
	private DeletePaymentConfigurationUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->repository = new MockPaymentConfigurationRepository();
		$this->use_case = new DeletePaymentConfigurationUseCase($this->repository);
	}

	public function test_execute_deletes_payment_configuration_successfully(): void
	{
		$saved = $this->repository->save(PaymentConfiguration::create('Perfil Deletavel', 1, ['pix'], null, true));

		$result = $this->use_case->execute($saved->get_id());

		$this->assertTrue($result);
		$this->assertNull($this->repository->find_by_id($saved->get_id()));
	}

	public function test_execute_throws_not_found_when_not_exists(): void
	{
		$this->expectException(NotFoundException::class);
		$this->expectExceptionMessage('Payment configuration not found');

		$this->use_case->execute(999);
	}

	public function test_execute_throws_conflict_when_has_linked_products(): void
	{
		$saved = $this->repository->save(PaymentConfiguration::create('Perfil Vinculado', 1, ['pix'], null, true));
		$this->repository->set_has_linked_products($saved->get_id(), true);

		$this->expectException(ConflictException::class);
		$this->expectExceptionMessage('Cannot delete payment configuration with linked products');

		$this->use_case->execute($saved->get_id());
	}
}
