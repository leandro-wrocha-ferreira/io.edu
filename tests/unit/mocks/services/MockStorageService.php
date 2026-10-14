<?php

namespace tests\unit\mocks\services;

use app\domain\storage\StorageServiceInterface;

/**
 * Mock storage service for unit tests.
 */
class MockStorageService implements StorageServiceInterface
{
	/**
	 * Simulated multipart sessions.
	 *
	 * @var array<string, array{key: string, folder: string, parts: array<int, string>}>
	 */
	public array $sessions = [];

	/**
	 * Auto-increment counter.
	 *
	 * @var int
	 */
	private int $counter = 1;

	/**
	 * Initiate multipart.
	 *
	 * @param string $filename
	 * @param string $content_type
	 * @param string $folder
	 * @return array{upload_id: string, key: string, bucket: string}
	 */
	public function initiate_multipart(string $filename, string $content_type, string $folder = 'uploads'): array
	{
		$upload_id = 'mock-upload-' . ($this->counter++);
		$key = $folder . '/' . $filename;

		$this->sessions[$upload_id] = [
			'key' => $key,
			'folder' => $folder,
			'parts' => [],
		];

		return [
			'upload_id' => $upload_id,
			'key' => $key,
			'bucket' => 'mock-bucket',
		];
	}

	/**
	 * Sign part.
	 *
	 * @param string $upload_id
	 * @param string $key
	 * @param int $part_number
	 * @return string
	 */
	public function sign_part(string $upload_id, string $key, int $part_number): string
	{
		return 'https://mock-bucket.s3.amazonaws.com/' . $key . '?partNumber=' . $part_number . '&uploadId=' . $upload_id;
	}

	/**
	 * List parts.
	 *
	 * @param string $upload_id
	 * @param string $key
	 * @return array<array{PartNumber: int, ETag: string}>
	 */
	public function list_parts(string $upload_id, string $key): array
	{
		return $this->sessions[$upload_id]['parts'] ?? [];
	}

	/**
	 * Complete multipart.

	 *
	 * @param string $upload_id
	 * @param string $key
	 * @param array<array{PartNumber: int, ETag: string}> $parts
	 * @return array{location: string, key: string, bucket: string}
	 */
	public function complete_multipart(string $upload_id, string $key, array $parts): array
	{
		return [
			'location' => 'https://mock-bucket.s3.amazonaws.com/' . $key,
			'key' => $key,
			'bucket' => 'mock-bucket',
		];
	}
}
