<?php

namespace tests\unit\domain\finance\value_objects;

use app\domain\exceptions\ValidationException;
use app\domain\finance\value_objects\EncryptedSecret;
use Credential_encryption_library;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for EncryptedSecret value object.
 */
class EncryptedSecretTest extends TestCase
{
	private Credential_encryption_library $encryption_library;

	/**
	 * Set up test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		parent::setUp();
		$this->encryption_library = new Credential_encryption_library('test-secret-key-32-chars-long!');
	}

	/**
	 * Test from_plain_text creates a valid EncryptedSecret.
	 *
	 * @return void
	 */
	public function test_from_plain_text_creates_valid_encrypted_secret(): void
	{
		$secret = EncryptedSecret::from_plain_text('sk_test_1234567890abcdef', $this->encryption_library);

		$this->assertNotEmpty($secret->get_payload());
		$this->assertNotSame('sk_test_1234567890abcdef', $secret->get_payload());
		$this->assertSame($secret->get_payload(), (string) $secret);
		$this->assertSame('sk_test_1234567890abcdef', $secret->decrypt($this->encryption_library));
	}

	/**
	 * Test from_encrypted creates a valid EncryptedSecret from existing payload.
	 *
	 * @return void
	 */
	public function test_from_encrypted_creates_valid_instance(): void
	{
		$original_secret = EncryptedSecret::from_plain_text('my-webhook-secret-999', $this->encryption_library);
		$reconstituted = EncryptedSecret::from_encrypted($original_secret->get_payload());

		$this->assertSame($original_secret->get_payload(), $reconstituted->get_payload());
		$this->assertSame('my-webhook-secret-999', $reconstituted->decrypt($this->encryption_library));
	}

	/**
	 * Test that from_plain_text with empty string throws ValidationException.
	 *
	 * @return void
	 */
	public function test_from_plain_text_with_empty_string_throws_exception(): void
	{
		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Cannot encrypt empty credential plaintext');

		EncryptedSecret::from_plain_text('   ', $this->encryption_library);
	}

	/**
	 * Test that from_encrypted with empty string throws ValidationException.
	 *
	 * @return void
	 */
	public function test_from_encrypted_with_empty_string_throws_exception(): void
	{
		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Encrypted credential payload cannot be empty');

		EncryptedSecret::from_encrypted('   ');
	}

	/**
	 * Test that from_encrypted with invalid payload throws ValidationException.
	 *
	 * @return void
	 */
	public function test_from_encrypted_with_invalid_payload_throws_exception(): void
	{
		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Invalid encrypted payload structure');

		EncryptedSecret::from_encrypted('short-payload');
	}
}
