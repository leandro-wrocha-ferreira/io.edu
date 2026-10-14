<?php

namespace tests\unit\domain\course\blocks;

use app\domain\course\blocks\AttachmentBlock;
use app\domain\exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for AttachmentBlock domain entity.
 */
class AttachmentBlockTest extends TestCase
{
	/**
	 * Test creating an attachment block successfully.
	 *
	 * @return void
	 */
	public function test_create_attachment_block_success(): void
	{
		$block = AttachmentBlock::create_attachment(
			1,
			'Guia_DDD.pdf',
			'uploads/courses/guia.pdf',
			10,
			1048576,
			'application/pdf',
			3,
			true
		);

		$this->assertSame('attachment', $block->get_type());
		$this->assertSame('Guia_DDD.pdf', $block->get_filename());
		$this->assertSame('uploads/courses/guia.pdf', $block->get_filepath());
		$this->assertSame(10, $block->get_min_reading_time_in_minutes());
		$this->assertSame(600, $block->get_min_reading_time_in_seconds());
		$this->assertSame(1048576, $block->get_file_size());
		$this->assertSame('application/pdf', $block->get_file_type());
		$this->assertTrue($block->is_required());
		$this->assertSame([
			'filename' => 'Guia_DDD.pdf',
			'filepath' => 'uploads/courses/guia.pdf',
			'file_size' => 1048576,
			'file_type' => 'application/pdf',
			'min_reading_time_in_minutes' => 10,
		], $block->get_payload());
	}

	/**
	 * Test updating an attachment block.
	 *
	 * @return void
	 */
	public function test_update_attachment_block(): void
	{
		$block = AttachmentBlock::create_attachment(1, 'old.pdf', 'path/old.pdf');
		$block->update_attachment('new.pdf', 'path/new.pdf', 5, 2048, 'pdf', false);

		$this->assertSame('new.pdf', $block->get_filename());
		$this->assertSame('path/new.pdf', $block->get_filepath());
		$this->assertSame(5, $block->get_min_reading_time_in_minutes());
		$this->assertFalse($block->is_required());
	}

	/**
	 * Test empty filename throws exception.
	 *
	 * @return void
	 */
	public function test_empty_filename_throws_exception(): void
	{
		$this->expectException(ValidationException::class);
		AttachmentBlock::create_attachment(1, '', 'path.pdf');
	}

	/**
	 * Test empty filepath throws exception.
	 *
	 * @return void
	 */
	public function test_empty_filepath_throws_exception(): void
	{
		$this->expectException(ValidationException::class);
		AttachmentBlock::create_attachment(1, 'arquivo.pdf', '');
	}
}
