<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use app\factories\LibraryFactory;
use app\usecases\storage\CompleteMultipartUploadUseCase;
use app\usecases\storage\InitiateMultipartUploadUseCase;
use app\usecases\storage\ListMultipartUploadPartsUseCase;
use app\usecases\storage\SignMultipartUploadPartUseCase;

/**
 * Controller for managing storage and S3 multipart upload operations.
 */
class Storage extends MY_Controller
{
	/**
	 * Initiate a multipart upload session.
	 *
	 * @return void
	 */
	public function initiate_multipart()
	{
		$filename = (string) $this->get_input('filename');
		$content_type = (string) ($this->get_input('content_type') ?: 'application/octet-stream');
		$folder = (string) ($this->get_input('folder') ?: 'uploads');

		$use_case = new InitiateMultipartUploadUseCase(LibraryFactory::make('storage_library'));
		$result = $use_case->execute($filename, $content_type, $folder);

		json_response([
			'success' => true,
			'upload_id' => $result['upload_id'],
			'key' => $result['key'],
			'bucket' => $result['bucket'],
		]);
	}

	/**
	 * Generate a presigned URL for an upload part.
	 *
	 * @return void
	 */
	public function sign_part()
	{
		$upload_id = (string) $this->get_input('upload_id');
		$key = (string) $this->get_input('key');
		$part_number = (int) $this->get_input('part_number');

		$use_case = new SignMultipartUploadPartUseCase(LibraryFactory::make('storage_library'));
		$url = $use_case->execute($upload_id, $key, $part_number);

		json_response([
			'success' => true,
			'url' => $url,
		]);
	}

	/**
	 * List already uploaded parts for an ongoing multipart upload session.
	 *
	 * @return void
	 */
	public function list_parts()
	{
		$upload_id = (string) $this->get_input('upload_id');
		$key = (string) $this->get_input('key');

		$use_case = new ListMultipartUploadPartsUseCase(LibraryFactory::make('storage_library'));
		$parts = $use_case->execute($upload_id, $key);

		json_response([
			'success' => true,
			'parts' => $parts,
		]);
	}

	/**
	 * Complete a multipart upload session.
	 *
	 * @return void
	 */
	public function complete_multipart()
	{
		$upload_id = (string) $this->get_input('upload_id');
		$key = (string) $this->get_input('key');
		$raw_parts = $this->get_input('parts');
		$parts = is_array($raw_parts) ? $raw_parts : (json_decode((string) $raw_parts, true) ?? []);

		$use_case = new CompleteMultipartUploadUseCase(LibraryFactory::make('storage_library'));
		$result = $use_case->execute($upload_id, $key, $parts);

		json_response([
			'success' => true,
			'location' => $result['location'],
			'key' => $result['key'],
			'bucket' => $result['bucket'],
		]);
	}
}
