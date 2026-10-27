<?php

namespace tests\unit\usecases\finance;

use app\domain\finance\PaymentConfiguration;
use app\usecases\finance\ListActivePaymentConfigurationsUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockPaymentConfigurationRepository;

/**
 * Unit tests for ListActivePaymentConfigurationsUseCase.
 */
class ListActivePaymentConfigurationsUseCaseTest extends TestCase
{
	private MockPaymentConfigurationRepository $repository;
	private ListActivePaymentConfigurationsUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->repository = new MockPaymentConfigurationRepository();
		$this->use_case = new ListActivePaymentConfigurationsUseCase($this->repository);
	}

	public function test_list_active_returns_only_active_configurations(): void
	{
		$this->repository->save(PaymentConfiguration::create('Config Ativa', 1, ['credit_card', 'pix'], null, true));
		$this->repository->save(PaymentConfiguration::create('Config Inativa', 1, ['pix'], null, false));

		$results = $this->use_case->execute();

		$this->assertCount(1, $results);
		$this->assertSame('Config Ativa', $results[0]->get_name());
	}

	public function test_list_active_returns_empty_when_none_active(): void
	{
		$this->repository->save(PaymentConfiguration::create('Config Inativa', 1, ['pix'], null, false));

		$results = $this->use_case->execute();
		$this->assertSame([], $results);
	}
}
