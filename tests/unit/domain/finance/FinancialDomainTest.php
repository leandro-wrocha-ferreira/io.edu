<?php

namespace tests\unit\domain\finance;

use app\domain\exceptions\ValidationException;
use app\domain\finance\CostCenter;
use app\domain\finance\PaymentAccount;
use app\domain\finance\PaymentConfiguration;
use app\domain\finance\PaymentCredential;
use app\domain\finance\PaymentProvider;
use app\domain\finance\value_objects\EncryptedSecret;
use Credential_encryption_library;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for financial domain entities.
 */
class FinancialDomainTest extends TestCase
{
	public function test_payment_provider_create(): void
	{
		$provider = PaymentProvider::create('Asaas', 'asaas', ['credit_card', 'pix', 'boleto']);
		$this->assertSame('Asaas', $provider->get_name());
		$this->assertSame('asaas', $provider->get_slug());
		$this->assertTrue($provider->supports_method('pix'));
		$this->assertFalse($provider->supports_method('crypto'));
	}

	public function test_payment_provider_empty_name_throws_validation_exception(): void
	{
		$this->expectException(ValidationException::class);
		PaymentProvider::create('', 'slug', ['pix']);
	}

	public function test_payment_credential_create(): void
	{
		$encryption_library = new Credential_encryption_library('test-key-32-chars-long!');
		$secret = EncryptedSecret::from_plain_text('sk_test_api_key_123', $encryption_library);

		$credential = PaymentCredential::create(
			1,
			'Credencial Asaas Sandbox',
			PaymentCredential::ENV_SANDBOX,
			$secret
		);

		$this->assertSame(1, $credential->get_provider_id());
		$this->assertSame(PaymentCredential::ENV_SANDBOX, $credential->get_environment());
		$this->assertTrue($credential->is_active());
		$this->assertSame($secret, $credential->get_api_key());
	}

	public function test_payment_credential_invalid_environment_throws(): void
	{
		$encryption_library = new Credential_encryption_library('test-key-32-chars-long!');
		$secret = EncryptedSecret::from_plain_text('sk_test_api_key_123', $encryption_library);

		$this->expectException(ValidationException::class);
		PaymentCredential::create(1, 'Name', 'staging', $secret);
	}

	public function test_payment_account_create(): void
	{
		$account = PaymentAccount::create(
			1,
			2,
			'Subconta Principal',
			PaymentCredential::ENV_PRODUCTION,
			'acc_12345',
			'12.345.678/0001-90',
			PaymentAccount::STATUS_ACTIVE
		);

		$this->assertSame('acc_12345', $account->get_account_identifier());
		$this->assertTrue($account->is_active());
	}

	public function test_cost_center_create(): void
	{
		$cost_center = CostCenter::create('Cursos Presenciais');
		$this->assertSame('Cursos Presenciais', $cost_center->get_name());
		$this->assertTrue($cost_center->is_active());

		$cost_center->deactivate();
		$this->assertFalse($cost_center->is_active());

		$cost_center->activate();
		$this->assertTrue($cost_center->is_active());
	}

	public function test_payment_configuration_create(): void
	{
		$config = PaymentConfiguration::create('Perfil Default', 10, ['pix', 'credit_card']);
		$this->assertSame('Perfil Default', $config->get_name());
		$this->assertSame(10, $config->get_payment_account_id());
		$this->assertTrue($config->supports_method('pix'));
		$this->assertFalse($config->supports_method('boleto'));
		$this->assertTrue($config->is_active());
	}

	public function test_payment_configuration_empty_methods_throws(): void
	{
		$this->expectException(ValidationException::class);
		PaymentConfiguration::create('Sem Métodos', 10, []);
	}
}
