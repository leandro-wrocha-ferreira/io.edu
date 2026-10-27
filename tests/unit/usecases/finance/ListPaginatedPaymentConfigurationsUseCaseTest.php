<?php

namespace tests\unit\usecases\finance;

use app\domain\finance\PaymentConfiguration;
use app\usecases\finance\ListPaginatedPaymentConfigurationsUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockPaymentConfigurationRepository;

/**
 * Unit tests for ListPaginatedPaymentConfigurationsUseCase.
 */
class ListPaginatedPaymentConfigurationsUseCaseTest extends TestCase
{
	private MockPaymentConfigurationRepository $repository;
	private ListPaginatedPaymentConfigurationsUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->repository = new MockPaymentConfigurationRepository();
		$this->use_case = new ListPaginatedPaymentConfigurationsUseCase($this->repository);
	}

	public function test_execute_returns_paginated_and_filtered_payment_configurations(): void
	{
		$this->repository->save(PaymentConfiguration::create('Asaas Produção', 1, ['credit_card', 'pix'], 'Desc', true));
		$this->repository->save(PaymentConfiguration::create('Asaas Sandbox', 1, ['pix'], null, false));

		$result = $this->use_case->execute(0, 10, 'Produção', 'name', 'ASC');

		$this->assertCount(1, $result['data']);
		$this->assertSame(1, $result['recordsFiltered']);
		$this->assertSame(2, $result['recordsTotal']);
		$this->assertSame('Asaas Produção', $result['data'][0]->get_name());
	}

	public function test_execute_with_status_filter(): void
	{
		$this->repository->save(PaymentConfiguration::create('Ativo 1', 1, ['pix'], null, true));
		$this->repository->save(PaymentConfiguration::create('Inativo 1', 1, ['pix'], null, false));

		$result = $this->use_case->execute(0, 10, '', 'name', 'ASC', 0);

		$this->assertCount(1, $result['data']);
		$this->assertSame('Inativo 1', $result['data'][0]->get_name());
	}
}
