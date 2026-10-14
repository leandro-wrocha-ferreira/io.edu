<?php

namespace app\usecases\storage;

use app\domain\exceptions\ValidationException;
use app\domain\storage\StorageServiceInterface;

/**
 * Use case for listing parts already uploaded in an active multipart upload session.
 */
class ListMultipartUploadPartsUseCase
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
	 * Execute listing.
	 *
	 * @param string $upload_id Multipart upload session ID
	 * @param string $key Object key
	 * @return array<array{PartNumber: int, ETag: string}>
	 * @throws ValidationException
	 */
	public function execute(string $upload_id, string $key): array
	{
		$trimmed_upload_id = trim($upload_id);
		$trimmed_key = trim($key);

		if ($trimmed_upload_id === '' || $trimmed_key === '') {
			throw new ValidationException("Upload ID and key are required");
		}

		return $this->storage_service->list_parts($trimmed_upload_id, $trimmed_key);
	}
}
