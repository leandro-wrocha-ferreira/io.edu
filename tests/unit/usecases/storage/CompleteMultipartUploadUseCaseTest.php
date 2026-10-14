<?php

namespace tests\unit\usecases\storage;

use app\domain\exceptions\ValidationException;
use app\usecases\storage\CompleteMultipartUploadUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\services\MockStorageService;

/**
 * Unit test for CompleteMultipartUploadUseCase.
 */
class CompleteMultipartUploadUseCaseTest extends TestCase
{
	private MockStorageService $storage_service;
	private CompleteMultipartUploadUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->storage_service = new MockStorageService();
		$this->use_case = new CompleteMultipartUploadUseCase($this->storage_service);
	}

	public function test_complete_multipart_upload_success(): void
	{
		$parts = [
			['PartNumber' => 1, 'ETag' => '"etag-1"'],
			['PartNumber' => 2, 'ETag' => '"etag-2"'],
		];

		$result = $this->use_case->execute('upload-123', 'videos/aula1.mp4', $parts);

		$this->assertStringContainsString('videos/aula1.mp4', $result['location']);
		$this->assertSame('videos/aula1.mp4', $result['key']);
		$this->assertSame('mock-bucket', $result['bucket']);
	}

	public function test_complete_multipart_empty_parts_throws_exception(): void
	{
		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Parts list cannot be empty');

		$this->use_case->execute('upload-123', 'videos/aula1.mp4', []);
	}
}
