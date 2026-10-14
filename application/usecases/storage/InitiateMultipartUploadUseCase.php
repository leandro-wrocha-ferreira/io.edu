<?php

namespace app\usecases\storage;

use app\domain\exceptions\ValidationException;
use app\domain\storage\StorageServiceInterface;

/**
 * Use case for initiating a multipart upload session.
 */
class InitiateMultipartUploadUseCase
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
	 * Execute initiation.
	 *
	 * @param string $filename Original file name
	 * @param string $content_type MIME type
	 * @param string $folder Destination folder
	 * @return array{upload_id: string, key: string, bucket: string}
	 * @throws ValidationException
	 */
	public function execute(string $filename, string $content_type = 'application/octet-stream', string $folder = 'uploads'): array
	{
		$trimmed_filename = trim($filename);
		if ($trimmed_filename === '') {
			throw new ValidationException("Filename cannot be empty");
		}

		return $this->storage_service->initiate_multipart($trimmed_filename, $content_type, $folder);
	}
}
