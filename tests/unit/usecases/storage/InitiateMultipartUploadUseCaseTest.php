<?php

namespace tests\unit\usecases\storage;

use app\domain\exceptions\ValidationException;
use app\usecases\storage\InitiateMultipartUploadUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\services\MockStorageService;

/**
 * Unit test for InitiateMultipartUploadUseCase.
 */
class InitiateMultipartUploadUseCaseTest extends TestCase
{
	private MockStorageService $storage_service;
	private InitiateMultipartUploadUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->storage_service = new MockStorageService();
		$this->use_case = new InitiateMultipartUploadUseCase($this->storage_service);
	}

	public function test_initiate_multipart_upload_success(): void
	{
		$result = $this->use_case->execute('aula1.mp4', 'video/mp4', 'videos');

		$this->assertNotEmpty($result['upload_id']);
		$this->assertSame('videos/aula1.mp4', $result['key']);
		$this->assertSame('mock-bucket', $result['bucket']);
	}

	public function test_initiate_multipart_upload_empty_filename_throws_exception(): void
	{
		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Filename cannot be empty');

		$this->use_case->execute('   ', 'video/mp4');
	}
}
