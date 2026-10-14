<?php

namespace tests\unit\domain\course\blocks;

use app\domain\course\blocks\VideoBlock;
use app\domain\exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for VideoBlock domain entity.
 */
class VideoBlockTest extends TestCase
{
	public function test_create_video_block_success(): void
	{
		$block = VideoBlock::create_video(1, 'vimeo', 'https://vimeo.com/123456', 900, 1, true);

		$this->assertSame('video', $block->get_type());
		$this->assertSame('vimeo', $block->get_provider());
		$this->assertSame('https://vimeo.com/123456', $block->get_url());
		$this->assertSame(900, $block->get_duration_in_seconds());
		$this->assertTrue($block->is_required());
		$this->assertSame([
			'provider' => 'vimeo',
			'url' => 'https://vimeo.com/123456',
			'duration_in_seconds' => 900,
		], $block->get_payload());
	}

	public function test_update_video_block(): void
	{
		$block = VideoBlock::create_video(1, 'youtube', 'https://youtube.com/watch?v=1');
		$block->update_video('bunny', 'https://video.bunnycdn.com/play/123', 1200, false);

		$this->assertSame('bunny', $block->get_provider());
		$this->assertSame('https://video.bunnycdn.com/play/123', $block->get_url());
		$this->assertSame(1200, $block->get_duration_in_seconds());
		$this->assertFalse($block->is_required());
	}

	public function test_empty_url_throws_exception(): void
	{
		$this->expectException(ValidationException::class);
		VideoBlock::create_video(1, 'youtube', '   ');
	}
}
