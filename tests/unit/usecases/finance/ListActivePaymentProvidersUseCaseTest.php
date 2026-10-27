<?php

namespace tests\unit\usecases\finance;

use app\domain\finance\PaymentProvider;
use app\usecases\finance\ListActivePaymentProvidersUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockPaymentProviderRepository;

/**
 * Unit tests for ListActivePaymentProvidersUseCase.
 */
class ListActivePaymentProvidersUseCaseTest extends TestCase
{
	private MockPaymentProviderRepository $repository;
	private ListActivePaymentProvidersUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->repository = new MockPaymentProviderRepository();
		$this->use_case = new ListActivePaymentProvidersUseCase($this->repository);
	}

	/**
	 * Test listing active payment providers returns only active ones.
	 *
	 * @return void
	 */
	public function test_list_active_returns_only_active_payment_providers(): void
	{
		$this->repository->save(PaymentProvider::create('Stripe', 'stripe', ['credit_card', 'pix'], true));
		$this->repository->save(PaymentProvider::create('Asaas', 'asaas', ['pix', 'boleto'], false));

		$results = $this->use_case->execute();

		$this->assertCount(1, $results);
		$this->assertSame('stripe', $results[0]->get_slug());
	}

	/**
	 * Test listing active payment providers returns empty array when none are active.
	 *
	 * @return void
	 */
	public function test_list_active_returns_empty_when_none_active(): void
	{
		$this->repository->save(PaymentProvider::create('Asaas', 'asaas', ['pix', 'boleto'], false));

		$results = $this->use_case->execute();
		$this->assertSame([], $results);
	}
}
