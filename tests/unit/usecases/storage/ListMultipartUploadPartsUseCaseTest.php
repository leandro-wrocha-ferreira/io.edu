<?php

namespace tests\unit\usecases\storage;

use app\domain\exceptions\ValidationException;
use app\usecases\storage\ListMultipartUploadPartsUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\services\MockStorageService;

/**
 * Unit test for ListMultipartUploadPartsUseCase.
 */
class ListMultipartUploadPartsUseCaseTest extends TestCase
{
	private MockStorageService $storage_service;
	private ListMultipartUploadPartsUseCase $use_case;

	protected function setUp(): void
	{
		parent::setUp();
		$this->storage_service = new MockStorageService();
		$this->use_case = new ListMultipartUploadPartsUseCase($this->storage_service);
	}

	/**
	 * Test listing parts successfully.
	 *
	 * @return void
	 */
	public function test_list_parts_success(): void
	{
		$this->storage_service->sessions['upload-123'] = [
			'key' => 'videos/aula1.mp4',
			'folder' => 'videos',
			'parts' => [
				['PartNumber' => 1, 'ETag' => '"etag-1"'],
				['PartNumber' => 2, 'ETag' => '"etag-2"'],
			],
		];

		$parts = $this->use_case->execute('upload-123', 'videos/aula1.mp4');

		$this->assertCount(2, $parts);
		$this->assertSame(1, $parts[0]['PartNumber']);
	}

	/**
	 * Test listing parts throws validation exception when upload ID is empty.
	 *
	 * @return void
	 */
	public function test_list_parts_empty_id_throws_exception(): void
	{
		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Upload ID and key are required');

		$this->use_case->execute('', 'videos/aula1.mp4');
	}
}
