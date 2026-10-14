<?php

namespace tests\unit\domain\course;

use app\domain\course\blocks\AttachmentBlock;
use app\domain\course\blocks\QuizBlock;
use app\domain\course\blocks\TextBlock;
use app\domain\course\blocks\VideoBlock;
use app\domain\course\LessonBlock;
use app\domain\exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for polymorphic LessonBlock domain factory and common methods.
 */
class LessonBlockTest extends TestCase
{
	public function test_create_video_block_polymorphic(): void
	{
		$block = LessonBlock::create(
			1,
			'video',
			['provider' => 'youtube', 'url' => 'https://youtube.com/watch?v=abc', 'duration_in_seconds' => 300],
			1
		);

		$this->assertInstanceOf(VideoBlock::class, $block);
		$this->assertSame(1, $block->get_lesson_id());
		$this->assertSame('video', $block->get_type());
		$this->assertSame('youtube', $block->get_payload()['provider']);
		$this->assertSame(300, $block->get_payload()['duration_in_seconds']);
		$this->assertSame(1, $block->get_sort_order());
		$this->assertTrue($block->is_required());
		$this->assertFalse($block->is_deleted());
	}

	/**
	 * Test creating a text block polymorphically.
	 *
	 * @return void
	 */
	public function test_create_text_block_polymorphic(): void
	{
		$block = LessonBlock::create(
			1,
			'text',
			['content' => 'Conteúdo de aula', 'min_reading_time_in_minutes' => 2],
			2
		);

		$this->assertInstanceOf(TextBlock::class, $block);
		$this->assertSame(2, $block->get_payload()['min_reading_time_in_minutes']);
		$this->assertSame('Conteúdo de aula', $block->get_payload()['content']);
	}

	/**
	 * Test creating an attachment block polymorphically.
	 *
	 * @return void
	 */
	public function test_create_attachment_block_polymorphic(): void
	{
		$block = LessonBlock::create(
			1,
			'attachment',
			['filename' => 'slides.pdf', 'filepath' => 'uploads/slides.pdf', 'min_reading_time_in_minutes' => 3],
			3,
			false
		);

		$this->assertInstanceOf(AttachmentBlock::class, $block);
		$this->assertFalse($block->is_required());
		$this->assertSame(3, $block->get_payload()['min_reading_time_in_minutes']);
	}

	public function test_create_quiz_block_polymorphic(): void
	{
		$block = LessonBlock::create(
			1,
			'quiz',
			['question' => 'O que é DDD?', 'explanation' => 'Domain Driven Design'],
			4
		);

		$this->assertInstanceOf(QuizBlock::class, $block);
		$this->assertSame('O que é DDD?', $block->get_payload()['question']);
	}

	public function test_block_change_sort_order(): void
	{
		$block = LessonBlock::create(1, 'attachment', ['filename' => 'code.zip', 'filepath' => 'uploads/code.zip'], 1);
		$block->change_sort_order(5);

		$this->assertSame(5, $block->get_sort_order());
	}

	public function test_block_delete(): void
	{
		$block = LessonBlock::create(1, 'quiz', ['question' => 'Pergunta?']);
		$block->delete();

		$this->assertTrue($block->is_deleted());
	}

	public function test_invalid_block_type_throws_exception(): void
	{
		$this->expectException(ValidationException::class);
		LessonBlock::create(1, 'invalid_type', []);
	}
}
