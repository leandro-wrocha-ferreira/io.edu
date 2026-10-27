<?php

namespace tests\unit\usecases\finance;

use app\domain\exceptions\NotFoundException;
use app\domain\finance\PaymentAccount;
use app\domain\finance\PaymentConfiguration;
use app\domain\finance\PaymentCredential;
use app\domain\finance\PaymentProvider;
use app\domain\finance\value_objects\EncryptedSecret;
use Credential_encryption_library;
use app\usecases\finance\GetPaymentConfigurationDetailUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockPaymentAccountRepository;
use tests\unit\mocks\repositories\MockPaymentConfigurationRepository;
use tests\unit\mocks\repositories\MockPaymentCredentialRepository;
use tests\unit\mocks\repositories\MockPaymentProviderRepository;

/**
 * Unit tests for GetPaymentConfigurationDetailUseCase.
 */
class GetPaymentConfigurationDetailUseCaseTest extends TestCase
{
	private MockPaymentConfigurationRepository $config_repo;
	private MockPaymentAccountRepository $account_repo;
	private MockPaymentCredentialRepository $credential_repo;
	private MockPaymentProviderRepository $provider_repo;
	private Credential_encryption_library $encryption_service;
	private GetPaymentConfigurationDetailUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->config_repo = new MockPaymentConfigurationRepository();
		$this->account_repo = new MockPaymentAccountRepository();
		$this->credential_repo = new MockPaymentCredentialRepository();
		$this->provider_repo = new MockPaymentProviderRepository();
		$this->encryption_service = new Credential_encryption_library('test-secret-key-32-chars-long!');

		$this->use_case = new GetPaymentConfigurationDetailUseCase(
			$this->config_repo,
			$this->account_repo,
			$this->credential_repo,
			$this->provider_repo,
			$this->encryption_service
		);
	}

	public function test_execute_returns_full_detail_successfully(): void
	{
		$provider = $this->provider_repo->save(PaymentProvider::create('Asaas', 'asaas', ['pix', 'credit_card', 'boleto'], true));
		$credential = $this->credential_repo->save(PaymentCredential::create(
			$provider->get_id(),
			'Credencial Asaas',
			PaymentCredential::ENV_PRODUCTION,
			EncryptedSecret::from_plain_text('fake_api_key_1234567890abcdef', $this->encryption_service)
		));
		$account = $this->account_repo->save(PaymentAccount::create(
			$provider->get_id(),
			$credential->get_id(),
			'Conta Asaas',
			PaymentCredential::ENV_PRODUCTION,
			'acc_12345',
			'12.345.678/0001-90'
		));
		$config = $this->config_repo->save(PaymentConfiguration::create(
			'Perfil Asaas Principal',
			$account->get_id(),
			['pix', 'credit_card'],
			'Descrição',
			true
		));

		$result = $this->use_case->execute($config->get_id());

		$this->assertSame($config->get_id(), $result['configuration']->get_id());
		$this->assertSame($account->get_id(), $result['account']->get_id());
		$this->assertSame($credential->get_id(), $result['credential']->get_id());
		$this->assertSame($provider->get_id(), $result['provider']->get_id());
		$this->assertStringContainsString('****', $result['masked_api_key']);
		$this->assertStringStartsWith('fake', $result['masked_api_key']);
	}

	public function test_execute_throws_not_found_when_configuration_not_found(): void
	{
		$this->expectException(NotFoundException::class);
		$this->expectExceptionMessage('Payment configuration not found');

		$this->use_case->execute(999);
	}
}
