<?php

namespace app\usecases\storage;

use app\domain\exceptions\ValidationException;
use app\domain\storage\StorageServiceInterface;

/**
 * Use case for completing a multipart upload session.
 */
class CompleteMultipartUploadUseCase
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
	 * Execute completion.
	 *
	 * @param string $upload_id Multipart upload session ID
	 * @param string $key Object key
	 * @param array<array{PartNumber: int, ETag: string}> $parts Uploaded parts list
	 * @return array{location: string, key: string, bucket: string}
	 * @throws ValidationException
	 */
	public function execute(string $upload_id, string $key, array $parts): array
	{
		$trimmed_upload_id = trim($upload_id);
		$trimmed_key = trim($key);

		if ($trimmed_upload_id === '' || $trimmed_key === '') {
			throw new ValidationException("Upload ID and key are required");
		}

		if (empty($parts)) {
			throw new ValidationException("Parts list cannot be empty");
		}

		return $this->storage_service->complete_multipart($trimmed_upload_id, $trimmed_key, $parts);
	}
}
