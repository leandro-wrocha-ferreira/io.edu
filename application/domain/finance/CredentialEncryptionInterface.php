<?php

namespace app\domain\finance;

/**
 * Contract for symmetric credential encryption operations.
 */
interface CredentialEncryptionInterface
{
	/**
	 * Encrypt sensitive plaintext into a secure ciphertext payload.
	 *
	 * @param string $plaintext Raw sensitive secret
	 * @return string Encrypted base64 payload
	 */
	public function encrypt(string $plaintext): string;

	/**
	 * Decrypt an encrypted payload back into plaintext.
	 *
	 * @param string $payload Encrypted base64 payload
	 * @return string Decrypted plaintext
	 */
	public function decrypt(string $payload): string;
}
