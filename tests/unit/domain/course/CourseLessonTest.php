<?php

namespace tests\unit\domain\course;

use app\domain\course\CourseLesson;
use app\domain\course\LessonBlock;
use app\domain\exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for CourseLesson domain entity.
 */
class CourseLessonTest extends TestCase
{
	public function test_create_lesson_success(): void
	{
		$lesson = CourseLesson::create(
			1,
			'Aula 1',
			'Descrição da aula',
			1,
			'draft'
		);

		$this->assertSame(1, $lesson->get_module_id());
		$this->assertSame('Aula 1', $lesson->get_title());
		$this->assertSame('Descrição da aula', $lesson->get_description());
		$this->assertSame(1, $lesson->get_sort_order());
		$this->assertTrue($lesson->is_draft());
	}

	public function test_lesson_publish_and_unpublish(): void
	{
		$lesson = CourseLesson::create(1, 'Aula');
		$lesson->publish();
		$this->assertTrue($lesson->is_published());

		$lesson->unpublish();
		$this->assertTrue($lesson->is_draft());
	}

	public function test_lesson_update(): void
	{
		$lesson = CourseLesson::create(1, 'Original');
		$lesson->update('Modificada', 'Nova sinopse', 'published');

		$this->assertSame('Modificada', $lesson->get_title());
		$this->assertSame('Nova sinopse', $lesson->get_description());
		$this->assertTrue($lesson->is_published());
	}

	public function test_lesson_blocks_collection(): void
	{
		$lesson = CourseLesson::create(1, 'Aula com Blocos');
		$block = LessonBlock::create(1, 'video', ['url' => 'https://vimeo.com/123']);

		$lesson->set_blocks([$block]);
		$this->assertCount(1, $lesson->get_blocks());
	}

	public function test_lesson_empty_title_throws_exception(): void
	{
		$this->expectException(ValidationException::class);
		CourseLesson::create(1, '');
	}
}
