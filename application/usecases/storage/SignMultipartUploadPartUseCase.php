<?php

namespace app\usecases\storage;

use app\domain\exceptions\ValidationException;
use app\domain\storage\StorageServiceInterface;

/**
 * Use case for generating presigned upload URL for a specific part.
 */
class SignMultipartUploadPartUseCase
{
	/** @var StorageServiceInterface */
	private StorageServiceInterface $storage_service;

	/**
	 * Constructor.
	 *
	 * @param StorageServiceInterface $storage_service
	 */
	public function __construct(StorageServiceInterface $storage_service)
	{
		$this->storage_service = $storage_service;
	}

	/**
	 * Execute part signing.
	 *
	 * @param string $upload_id Multipart upload session ID
	 * @param string $key Object key
	 * @param int $part_number Part number (1-based)
	 * @return string Presigned upload URL
	 * @throws ValidationException
	 */
	public function execute(string $upload_id, string $key, int $part_number): string
	{
		$trimmed_upload_id = trim($upload_id);
		$trimmed_key = trim($key);

		if ($trimmed_upload_id === '' || $trimmed_key === '') {
			throw new ValidationException("Upload ID and key are required");
		}

		if ($part_number <= 0) {
			throw new ValidationException("Part number must be greater than zero");
		}

		return $this->storage_service->sign_part($trimmed_upload_id, $trimmed_key, $part_number);
	}
}
