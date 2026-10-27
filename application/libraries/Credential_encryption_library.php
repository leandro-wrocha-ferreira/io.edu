<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\exceptions\ValidationException;
use app\domain\finance\CredentialEncryptionInterface;

/**
 * CI3 Library providing AES-256-GCM symmetric encryption for payment credentials.
 */
class Credential_encryption_library implements CredentialEncryptionInterface
{
	private const CIPHER = 'aes-256-gcm';
	private const IV_LENGTH = 12; // 96-bit nonce for GCM
	private const TAG_LENGTH = 16; // 128-bit authentication tag

	/**
	 * Derived 32-byte binary key.
	 *
	 * @var string
	 */
	private string $encryption_key;

	/**
	 * Constructor.
	 *
	 * @param string|array<string, mixed>|null $config Optional custom key or config for testing
	 */
	public function __construct(string|array|null $config = null)
	{
		$raw_key = null;
		if (is_string($config) && $config !== '') {
			$raw_key = $config;
		} else {
			$raw_key = getenv('PAYMENT_ENCRYPTION_KEY') ?: ($_ENV['PAYMENT_ENCRYPTION_KEY'] ?? 'ioedu-payment-default-secret-key-32-chars!!');
		}

		$this->encryption_key = hash('sha256', (string) $raw_key, true);
	}

	/**
	 * Encrypt plaintext using AES-256-GCM.
	 *
	 * @param string $plaintext Raw sensitive secret (e.g. API key)
	 * @return string Base64 encoded payload containing IV + Tag + Ciphertext
	 * @throws ValidationException If encryption fails
	 */
	public function encrypt(string $plaintext): string
	{
		$clean_plaintext = trim($plaintext);
		if ($clean_plaintext === '') {
			throw new ValidationException("Cannot encrypt empty credential plaintext");
		}

		$initialization_vector = openssl_random_pseudo_bytes(self::IV_LENGTH);
		$authentication_tag = '';
		$ciphertext = openssl_encrypt(
			$clean_plaintext,
			self::CIPHER,
			$this->encryption_key,
			OPENSSL_RAW_DATA,
			$initialization_vector,
			$authentication_tag,
			'',
			self::TAG_LENGTH
		);

		if ($ciphertext === false) {
			throw new ValidationException("Failed to encrypt payment credential");
		}

		return base64_encode($initialization_vector . $authentication_tag . $ciphertext);
	}

	/**
	 * Decrypt AES-256-GCM encrypted payload.
	 *
	 * @param string $payload Base64 encoded payload
	 * @return string Decrypted plaintext
	 * @throws ValidationException If payload is invalid or authentication tag verification fails
	 */
	public function decrypt(string $payload): string
	{
		$decoded = base64_decode(trim($payload), true);
		$min_length = self::IV_LENGTH + self::TAG_LENGTH;

		if ($decoded === false || strlen($decoded) < $min_length) {
			throw new ValidationException("Invalid encrypted payload structure");
		}

		$initialization_vector = substr($decoded, 0, self::IV_LENGTH);
		$authentication_tag = substr($decoded, self::IV_LENGTH, self::TAG_LENGTH);
		$ciphertext = substr($decoded, $min_length);

		$decrypted = openssl_decrypt(
			$ciphertext,
			self::CIPHER,
			$this->encryption_key,
			OPENSSL_RAW_DATA,
			$initialization_vector,
			$authentication_tag
		);

		if ($decrypted === false) {
			throw new ValidationException("Credential decryption failed: invalid key or tampered data");
		}

		return $decrypted;
	}
}
