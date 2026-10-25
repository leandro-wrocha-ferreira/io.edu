<?php

namespace app\domain\finance\value_objects;

use app\domain\exceptions\ValidationException;
use app\domain\finance\CredentialEncryptionInterface;

/**
 * Value object encapsulating encrypted credential secrets with leak prevention.
 */
final class EncryptedSecret
{
	/**
	 * Minimum bytes for AES-256-GCM binary payload (12-byte IV + 16-byte Tag + ciphertext).
	 */
	private const MIN_PAYLOAD_BINARY_LENGTH = 28;

	/**
	 * Base64-encoded encrypted payload.
	 *
	 * @var string
	 */
	private string $payload;

	/**
	 * Private constructor.
	 *
	 * @param string $payload Validated encrypted payload
	 */
	private function __construct(string $payload)
	{
		$this->payload = $payload;
	}

	/**
	 * Create an EncryptedSecret instance from an existing encrypted base64 payload.
	 *
	 * @param string $payload Encrypted base64 payload
	 * @return self
	 * @throws ValidationException When payload is empty or invalid
	 */
	public static function from_encrypted(string $payload): self
	{
		$clean_payload = trim($payload);
		if ($clean_payload === '') {
			throw new ValidationException("Encrypted credential payload cannot be empty");
		}

		$decoded = base64_decode($clean_payload, true);
		if ($decoded === false || strlen($decoded) < self::MIN_PAYLOAD_BINARY_LENGTH) {
			throw new ValidationException("Invalid encrypted payload structure");
		}

		return new self($clean_payload);
	}

	/**
	 * Create an EncryptedSecret instance from plaintext using an encryption service.
	 *
	 * @param string $plaintext Raw secret text to encrypt
	 * @param CredentialEncryptionInterface $encryption_service Service implementing encryption contract
	 * @return self
	 * @throws ValidationException When plaintext is invalid or encryption fails
	 */
	public static function from_plain_text(string $plaintext, CredentialEncryptionInterface $encryption_service): self
	{
		$clean_plaintext = trim($plaintext);
		if ($clean_plaintext === '') {
			throw new ValidationException("Cannot encrypt empty credential plaintext");
		}

		$encrypted_payload = $encryption_service->encrypt($clean_plaintext);
		return self::from_encrypted($encrypted_payload);
	}

	/**
	 * Decrypt encapsulated secret using an encryption service.
	 *
	 * @param CredentialEncryptionInterface $encryption_service Service implementing encryption contract
	 * @return string Decrypted plaintext
	 */
	public function decrypt(CredentialEncryptionInterface $encryption_service): string
	{
		return $encryption_service->decrypt($this->payload);
	}

	/**
	 * Get the underlying encrypted base64 payload for persistence.
	 *
	 * @return string
	 */
	public function get_payload(): string
	{
		return $this->payload;
	}

	/**
	 * Return the encrypted payload as string.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->payload;
	}
}
