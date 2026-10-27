<?php

namespace tests\unit\usecases\finance;

use app\domain\exceptions\NotFoundException;
use app\domain\exceptions\ValidationException;
use app\domain\finance\PaymentCredential;
use app\domain\finance\PaymentProvider;
use Credential_encryption_library;
use app\usecases\finance\CreatePaymentConfigurationUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockPaymentAccountRepository;
use tests\unit\mocks\repositories\MockPaymentConfigurationRepository;
use tests\unit\mocks\repositories\MockPaymentCredentialRepository;
use tests\unit\mocks\repositories\MockPaymentProviderRepository;
use tests\unit\mocks\repositories\MockTransactionManager;

/**
 * Unit tests for CreatePaymentConfigurationUseCase.
 */
class CreatePaymentConfigurationUseCaseTest extends TestCase
{
	private MockPaymentConfigurationRepository $config_repo;
	private MockPaymentAccountRepository $account_repo;
	private MockPaymentCredentialRepository $credential_repo;
	private MockPaymentProviderRepository $provider_repo;
	private Credential_encryption_library $encryption_service;
	private MockTransactionManager $transaction_manager;
	private CreatePaymentConfigurationUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->config_repo = new MockPaymentConfigurationRepository();
		$this->account_repo = new MockPaymentAccountRepository();
		$this->credential_repo = new MockPaymentCredentialRepository();
		$this->provider_repo = new MockPaymentProviderRepository();
		$this->encryption_service = new Credential_encryption_library('test-secret-key-32-chars-long!');
		$this->transaction_manager = new MockTransactionManager();

		$this->use_case = new CreatePaymentConfigurationUseCase(
			$this->config_repo,
			$this->account_repo,
			$this->credential_repo,
			$this->provider_repo,
			$this->encryption_service,
			$this->transaction_manager
		);
	}

	public function test_execute_creates_configuration_account_and_credentials_atomically(): void
	{
		$provider = $this->provider_repo->save(PaymentProvider::create('Asaas', 'asaas', ['pix', 'credit_card', 'boleto'], true));

		$config = $this->use_case->execute(
			'Asaas Produção',
			$provider->get_id(),
			PaymentCredential::ENV_PRODUCTION,
			'fake_api_key_supersecretkey12345',
			['pix', 'credit_card'],
			'wh_sec_555',
			'Perfil principal de cobranças',
			true
		);

		$this->assertNotNull($config->get_id());
		$this->assertSame('Asaas Produção', $config->get_name());
		$this->assertSame(['pix', 'credit_card'], $config->get_allowed_methods());
		$this->assertTrue($config->is_active());

		// Verify account was created
		$account = $this->account_repo->find_by_id($config->get_payment_account_id());
		$this->assertNotNull($account);
		$this->assertNull($account->get_account_identifier());
		$this->assertNull($account->get_document_number());

		// Verify credential was encrypted
		$credential = $this->credential_repo->find_by_id($account->get_credential_id());
		$this->assertNotNull($credential);
		$this->assertNotSame('fake_api_key_supersecretkey12345', $credential->get_api_key_encrypted());
		$this->assertSame('fake_api_key_supersecretkey12345', $credential->get_api_key()->decrypt($this->encryption_service));
	}

	public function test_execute_throws_not_found_when_provider_not_found(): void
	{
		$this->expectException(NotFoundException::class);
		$this->expectExceptionMessage('Payment provider not found');

		$this->use_case->execute(
			'Config',
			999,
			PaymentCredential::ENV_SANDBOX,
			'key',
			['pix']
		);
	}

	public function test_execute_throws_validation_exception_when_api_key_empty(): void
	{
		$provider = $this->provider_repo->save(PaymentProvider::create('Asaas', 'asaas', ['pix'], true));

		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('API key cannot be empty');

		$this->use_case->execute(
			'Config',
			$provider->get_id(),
			PaymentCredential::ENV_SANDBOX,
			'  ',
			['pix']
		);
	}

	public function test_execute_throws_validation_exception_when_allowed_methods_empty(): void
	{
		$provider = $this->provider_repo->save(PaymentProvider::create('Asaas', 'asaas', ['pix'], true));

		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Payment configuration must have at least one allowed payment method');

		$this->use_case->execute(
			'Config',
			$provider->get_id(),
			PaymentCredential::ENV_SANDBOX,
			'sk_live_123',
			[]
		);
	}
}
