<?php

namespace tests\unit\usecases\course;

use app\domain\course\blocks\AttachmentBlock;
use app\domain\course\blocks\TextBlock;
use app\domain\course\blocks\VideoBlock;
use app\domain\course\CourseLesson;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\CreateLessonBlockUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseLessonRepository;
use tests\unit\mocks\repositories\MockLessonBlockRepository;

/**
 * Unit tests for CreateLessonBlockUseCase.
 */
class CreateLessonBlockUseCaseTest extends TestCase
{
	private MockCourseLessonRepository $lesson_repository;
	private MockLessonBlockRepository $block_repository;
	private CreateLessonBlockUseCase $use_case;
	private CourseLesson $lesson;

	protected function setUp(): void
	{
		parent::setUp();
		$this->lesson_repository = new MockCourseLessonRepository();
		$this->block_repository = new MockLessonBlockRepository();
		$this->use_case = new CreateLessonBlockUseCase($this->block_repository, $this->lesson_repository);

		$this->lesson = $this->lesson_repository->create(CourseLesson::create(
			1,
			'Aula para Blocos'
		));
	}

	public function test_create_block_success(): void
	{
		$block = $this->use_case->execute(
			(int) $this->lesson->get_id(),
			'text',
			['content' => '# Introdução\nTexto formatado em markdown.'],
			1
		);

		$this->assertInstanceOf(TextBlock::class, $block);
		$this->assertNotNull($block->get_id());
		$this->assertSame((int) $this->lesson->get_id(), $block->get_lesson_id());
		$this->assertSame('text', $block->get_type());
		$this->assertSame('# Introdução\nTexto formatado em markdown.', $block->get_payload()['content']);
		$this->assertSame(1, $block->get_sort_order());
		$this->assertTrue($block->is_required());
	}

	/**
	 * Test creating an optional attachment block successfully.
	 *
	 * @return void
	 */
	public function test_create_optional_attachment_block_success(): void
	{
		$block = $this->use_case->execute(
			(int) $this->lesson->get_id(),
			'attachment',
			['filename' => 'slides.pdf', 'filepath' => 'uploads/slides.pdf', 'min_reading_time_in_minutes' => 2],
			2,
			false
		);

		$this->assertInstanceOf(AttachmentBlock::class, $block);
		$this->assertFalse($block->is_required());
		$this->assertSame(2, $block->get_payload()['min_reading_time_in_minutes']);
	}

	public function test_create_video_block_success(): void
	{
		$block = $this->use_case->execute(
			(int) $this->lesson->get_id(),
			'video',
			['provider' => 'bunny', 'url' => 'https://bunny.net/123.m3u8', 'duration_in_seconds' => 720]
		);

		$this->assertInstanceOf(VideoBlock::class, $block);
		$this->assertSame(1, $block->get_sort_order());
		$this->assertTrue($block->is_required());
		$this->assertSame(720, $block->get_payload()['duration_in_seconds']);
	}

	public function test_create_block_with_non_existing_lesson_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999, 'text', ['content' => 'Teste']);
	}
}
