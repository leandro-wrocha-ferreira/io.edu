<?php

namespace tests\unit\domain\course\blocks;

use app\domain\course\blocks\TextBlock;
use app\domain\exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for TextBlock domain entity.
 */
class TextBlockTest extends TestCase
{
	/**
	 * Test creating a text block successfully.
	 *
	 * @return void
	 */
	public function test_create_text_block_success(): void
	{
		$block = TextBlock::create_text(1, 'Artigo sobre Clean Architecture', 5, 2, true);

		$this->assertSame('text', $block->get_type());
		$this->assertSame('Artigo sobre Clean Architecture', $block->get_content());
		$this->assertSame(5, $block->get_min_reading_time_in_minutes());
		$this->assertSame(300, $block->get_min_reading_time_in_seconds());
		$this->assertTrue($block->is_required());
		$this->assertSame([
			'content' => 'Artigo sobre Clean Architecture',
			'min_reading_time_in_minutes' => 5,
		], $block->get_payload());
	}

	/**
	 * Test updating a text block.
	 *
	 * @return void
	 */
	public function test_update_text_block(): void
	{
		$block = TextBlock::create_text(1, 'Conteúdo inicial');
		$block->update_text('Conteúdo atualizado', 3, false);

		$this->assertSame('Conteúdo atualizado', $block->get_content());
		$this->assertSame(3, $block->get_min_reading_time_in_minutes());
		$this->assertFalse($block->is_required());
	}

	/**
	 * Test empty content throws exception.
	 *
	 * @return void
	 */
	public function test_empty_content_throws_exception(): void
	{
		$this->expectException(ValidationException::class);
		TextBlock::create_text(1, '   ');
	}
}
