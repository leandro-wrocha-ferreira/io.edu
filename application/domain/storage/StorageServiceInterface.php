<?php

namespace app\domain\storage;

/**
 * Interface defining contract for cloud storage operations including S3 multipart upload.
 */
interface StorageServiceInterface
{
	/**
	 * Initiate multipart upload session.
	 *
	 * @param string $filename Original file name
	 * @param string $content_type MIME type
	 * @param string $folder Destination folder
	 * @return array{upload_id: string, key: string, bucket: string}
	 */
	public function initiate_multipart(string $filename, string $content_type, string $folder = 'uploads'): array;

	/**
	 * Generate presigned URL for an upload part.
	 *
	 * @param string $upload_id Multipart upload ID
	 * @param string $key Object key
	 * @param int $part_number Part number (1-based)
	 * @return string Presigned upload URL
	 */
	public function sign_part(string $upload_id, string $key, int $part_number): string;

	/**
	 * List already uploaded parts for an ongoing multipart upload session.
	 *
	 * @param string $upload_id Multipart upload ID
	 * @param string $key Object key
	 * @return array<array{PartNumber: int, ETag: string}>
	 */
	public function list_parts(string $upload_id, string $key): array;

	/**
	 * Complete a multipart upload session.
	 *
	 * @param string $upload_id Multipart upload ID
	 * @param string $key Object key
	 * @param array<array{PartNumber: int, ETag: string}> $parts Uploaded parts list
	 * @return array{location: string, key: string, bucket: string}
	 */
	public function complete_multipart(string $upload_id, string $key, array $parts): array;
}
