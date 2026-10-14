<?php

namespace tests\unit\usecases\storage;

use app\domain\exceptions\ValidationException;
use app\usecases\storage\SignMultipartUploadPartUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\services\MockStorageService;

/**
 * Unit test for SignMultipartUploadPartUseCase.
 */
class SignMultipartUploadPartUseCaseTest extends TestCase
{
	private MockStorageService $storage_service;
	private SignMultipartUploadPartUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->storage_service = new MockStorageService();
		$this->use_case = new SignMultipartUploadPartUseCase($this->storage_service);
	}

	public function test_sign_part_success(): void
	{
		$presigned_url = $this->use_case->execute('upload-123', 'videos/aula1.mp4', 1);

		$this->assertStringContainsString('uploadId=upload-123', $presigned_url);
		$this->assertStringContainsString('partNumber=1', $presigned_url);
	}

	public function test_sign_part_empty_upload_id_throws_exception(): void
	{
		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Upload ID and key are required');

		$this->use_case->execute('', 'videos/aula1.mp4', 1);
	}

	public function test_sign_part_invalid_part_number_throws_exception(): void
	{
		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Part number must be greater than zero');

		$this->use_case->execute('upload-123', 'videos/aula1.mp4', 0);
	}
}
