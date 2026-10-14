<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\storage\StorageServiceInterface;
use app\infrastructure\storage\S3StorageService;

/**
 * Storage Library for CodeIgniter 3.
 *
 * Implements StorageServiceInterface and acts as a generic storage provider adapter.
 */
class Storage_library implements StorageServiceInterface
{
	/**
	 * Underlying storage driver implementation.
	 *
	 * @var StorageServiceInterface
	 */
	private StorageServiceInterface $driver;

	/**
	 * Constructor.
	 *
	 * @param array<string, mixed> $config Configuration parameters
	 */
	public function __construct(array $config = [])
	{
		$this->driver = new S3StorageService($config);
	}

	/**
	 * Initiate multipart upload session.
	 *
	 * @param string $filename Original file name
	 * @param string $content_type MIME type
	 * @param string $folder Destination folder
	 * @return array{upload_id: string, key: string, bucket: string}
	 */
	public function initiate_multipart(string $filename, string $content_type, string $folder = 'uploads'): array
	{
		return $this->driver->initiate_multipart($filename, $content_type, $folder);
	}

	/**
	 * Generate presigned URL for an upload part.
	 *
	 * @param string $upload_id Multipart upload ID
	 * @param string $key Object key
	 * @param int $part_number Part number (1-based)
	 * @return string Presigned upload URL
	 */
	public function sign_part(string $upload_id, string $key, int $part_number): string
	{
		return $this->driver->sign_part($upload_id, $key, $part_number);
	}

	/**
	 * List already uploaded parts for an ongoing multipart upload session.
	 *
	 * @param string $upload_id Multipart upload ID
	 * @param string $key Object key
	 * @return array<array{PartNumber: int, ETag: string}>
	 */
	public function list_parts(string $upload_id, string $key): array
	{
		return $this->driver->list_parts($upload_id, $key);
	}

	/**
	 * Complete a multipart upload session.
	 *
	 * @param string $upload_id Multipart upload ID
	 * @param string $key Object key
	 * @param array<array{PartNumber: int, ETag: string}> $parts Uploaded parts list
	 * @return array{location: string, key: string, bucket: string}
	 */
	public function complete_multipart(string $upload_id, string $key, array $parts): array
	{
		return $this->driver->complete_multipart($upload_id, $key, $parts);
	}
}
