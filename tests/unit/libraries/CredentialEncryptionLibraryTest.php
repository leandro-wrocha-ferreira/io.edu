<?php

namespace tests\unit\libraries;

use app\domain\exceptions\ValidationException;
use Credential_encryption_library;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Credential_encryption_library CI3 library.
 */
class CredentialEncryptionLibraryTest extends TestCase
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
		$this->encryption_library = new Credential_encryption_library('test-master-key-32-bytes-long!!');
	}

	/**
	 * Test encryption and decryption returns the exact original plaintext.
	 *
	 * @return void
	 */
	public function test_encrypt_and_decrypt_returns_original_plaintext(): void
	{
		$secret = 'fake_api_key_1234567890abcdefghijklmnopqrstuvwxyz';
		$encrypted = $this->encryption_library->encrypt($secret);

		$this->assertNotSame($secret, $encrypted);
		$decrypted = $this->encryption_library->decrypt($encrypted);
		$this->assertSame($secret, $decrypted);
	}

	/**
	 * Test that encrypting empty plaintext throws ValidationException.
	 *
	 * @return void
	 */
	public function test_encrypt_empty_plaintext_throws_validation_exception(): void
	{
		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Cannot encrypt empty credential plaintext');

		$this->encryption_library->encrypt('   ');
	}

	/**
	 * Test that tampered ciphertext fails decryption and throws ValidationException.
	 *
	 * @return void
	 */
	public function test_tampered_ciphertext_fails_decryption(): void
	{
		$secret = 'sensitive-api-token';
		$encrypted = $this->encryption_library->encrypt($secret);

		$raw_data = base64_decode($encrypted);
		// Tamper with the last byte
		$tampered_raw = substr($raw_data, 0, -1) . chr(ord(substr($raw_data, -1)) ^ 0xFF);
		$tampered = base64_encode($tampered_raw);

		$this->expectException(ValidationException::class);
		$this->encryption_library->decrypt($tampered);
	}

	/**
	 * Test that invalid base64 payload throws ValidationException.
	 *
	 * @return void
	 */
	public function test_invalid_base64_payload_throws_exception(): void
	{
		$this->expectException(ValidationException::class);
		$this->encryption_library->decrypt('not-a-valid-payload');
	}

	/**
	 * Test that decryption with a different key fails and throws ValidationException.
	 *
	 * @return void
	 */
	public function test_different_keys_fail_decryption(): void
	{
		$service_first = new Credential_encryption_library('key-number-one-32-chars-long!!!!');
		$service_second = new Credential_encryption_library('key-number-two-32-chars-long!!!!');

		$encrypted = $service_first->encrypt('my-secret-key');

		$this->expectException(ValidationException::class);
		$service_second->decrypt($encrypted);
	}
}
