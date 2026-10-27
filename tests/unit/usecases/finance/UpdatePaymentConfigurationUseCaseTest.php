<?php

namespace tests\unit\usecases\finance;

use app\domain\exceptions\NotFoundException;
use app\domain\exceptions\ValidationException;
use app\domain\finance\PaymentAccount;
use app\domain\finance\PaymentConfiguration;
use app\domain\finance\PaymentCredential;
use app\domain\finance\value_objects\EncryptedSecret;
use Credential_encryption_library;
use app\usecases\finance\UpdatePaymentConfigurationUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockPaymentAccountRepository;
use tests\unit\mocks\repositories\MockPaymentConfigurationRepository;
use tests\unit\mocks\repositories\MockPaymentCredentialRepository;
use tests\unit\mocks\repositories\MockTransactionManager;

/**
 * Unit tests for UpdatePaymentConfigurationUseCase.
 */
class UpdatePaymentConfigurationUseCaseTest extends TestCase
{
	private MockPaymentConfigurationRepository $config_repo;
	private MockPaymentAccountRepository $account_repo;
	private MockPaymentCredentialRepository $credential_repo;
	private Credential_encryption_library $encryption_service;
	private MockTransactionManager $transaction_manager;
	private UpdatePaymentConfigurationUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->config_repo = new MockPaymentConfigurationRepository();
		$this->account_repo = new MockPaymentAccountRepository();
		$this->credential_repo = new MockPaymentCredentialRepository();
		$this->encryption_service = new Credential_encryption_library('test-secret-key-32-chars-long!');
		$this->transaction_manager = new MockTransactionManager();

		$this->use_case = new UpdatePaymentConfigurationUseCase(
			$this->config_repo,
			$this->account_repo,
			$this->credential_repo,
			$this->encryption_service,
			$this->transaction_manager
		);
	}

	public function test_execute_updates_configuration_and_reencrypts_key_when_provided(): void
	{
		$credential = $this->credential_repo->save(PaymentCredential::create(
			1,
			'Cred',
			PaymentCredential::ENV_SANDBOX,
			EncryptedSecret::from_plain_text('old_key', $this->encryption_service)
		));
		$account = $this->account_repo->save(PaymentAccount::create(
			1,
			$credential->get_id(),
			'Conta',
			PaymentCredential::ENV_SANDBOX
		));
		$config = $this->config_repo->save(PaymentConfiguration::create(
			'Config Antiga',
			$account->get_id(),
			['pix']
		));

		$updated = $this->use_case->execute(
			$config->get_id(),
			'Config Nova',
			PaymentCredential::ENV_PRODUCTION,
			['pix', 'boleto'],
			'new_super_key_123',
			'wh_sec_new',
			'Desc Nova',
			false
		);

		$this->assertSame('Config Nova', $updated->get_name());
		$this->assertFalse($updated->is_active());
		$this->assertSame(['pix', 'boleto'], $updated->get_allowed_methods());

		// Verify account updated
		$updated_account = $this->account_repo->find_by_id($config->get_payment_account_id());
		$this->assertSame(PaymentCredential::ENV_PRODUCTION, $updated_account->get_environment());

		// Verify key re-encrypted
		$updated_cred = $this->credential_repo->find_by_id($updated_account->get_credential_id());
		$this->assertSame('new_super_key_123', $updated_cred->get_api_key()->decrypt($this->encryption_service));
	}

	public function test_execute_maintains_existing_key_when_new_key_not_provided(): void
	{
		$credential = $this->credential_repo->save(PaymentCredential::create(
			1,
			'Cred',
			PaymentCredential::ENV_SANDBOX,
			EncryptedSecret::from_plain_text('preserve_this_key', $this->encryption_service)
		));
		$account = $this->account_repo->save(PaymentAccount::create(
			1,
			$credential->get_id(),
			'Conta',
			PaymentCredential::ENV_SANDBOX
		));
		$config = $this->config_repo->save(PaymentConfiguration::create(
			'Config',
			$account->get_id(),
			['pix']
		));

		$this->use_case->execute(
			$config->get_id(),
			'Config Atualizada',
			PaymentCredential::ENV_SANDBOX,
			['pix'],
			'' // Empty key -> maintain
		);

		$cred = $this->credential_repo->find_by_id($account->get_credential_id());
		$this->assertSame('preserve_this_key', $cred->get_api_key()->decrypt($this->encryption_service));
	}

	public function test_execute_throws_not_found_when_configuration_not_found(): void
	{
		$this->expectException(NotFoundException::class);
		$this->expectExceptionMessage('Payment configuration not found');

		$this->use_case->execute(999, 'Nome', PaymentCredential::ENV_SANDBOX, ['pix']);
	}

	public function test_execute_throws_validation_exception_when_allowed_methods_empty(): void
	{
		$credential = $this->credential_repo->save(PaymentCredential::create(
			1,
			'Cred',
			PaymentCredential::ENV_SANDBOX,
			EncryptedSecret::from_plain_text('key', $this->encryption_service)
		));
		$account = $this->account_repo->save(PaymentAccount::create(
			1,
			$credential->get_id(),
			'Conta',
			PaymentCredential::ENV_SANDBOX
		));
		$config = $this->config_repo->save(PaymentConfiguration::create(
			'Config',
			$account->get_id(),
			['pix']
		));

		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Payment configuration must have at least one allowed payment method');

		$this->use_case->execute($config->get_id(), 'Nome', PaymentCredential::ENV_SANDBOX, []);
	}
}
