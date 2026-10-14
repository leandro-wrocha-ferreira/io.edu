<?php

namespace tests\unit\domain\course;

use app\domain\course\blocks\VideoBlock;
use app\domain\course\CourseLesson;
use app\domain\course\CourseModule;
use app\domain\exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for CourseModule domain entity.
 */
class CourseModuleTest extends TestCase
{
	public function test_create_module_success(): void
	{
		$module = CourseModule::create(
			1,
			'Módulo 1',
			'Descrição',
			1,
			'draft'
		);

		$this->assertSame(1, $module->get_course_id());
		$this->assertSame('Módulo 1', $module->get_title());
		$this->assertSame('Descrição', $module->get_description());
		$this->assertSame(1, $module->get_sort_order());
		$this->assertTrue($module->is_draft());
		$this->assertFalse($module->is_published());
		$this->assertFalse($module->is_deleted());
	}

	public function test_module_publish_and_unpublish(): void
	{
		$module = CourseModule::create(1, 'Módulo');
		$module->publish();
		$this->assertTrue($module->is_published());

		$module->unpublish();
		$this->assertTrue($module->is_draft());
	}

	public function test_module_update(): void
	{
		$module = CourseModule::create(1, 'Original');
		$module->update('Modificado', 'Nova Desc', 'published');

		$this->assertSame('Modificado', $module->get_title());
		$this->assertSame('Nova Desc', $module->get_description());
		$this->assertTrue($module->is_published());
	}

	public function test_module_empty_title_throws_exception(): void
	{
		$this->expectException(ValidationException::class);
		CourseModule::create(1, '   ');
	}

	public function test_module_invalid_status_throws_exception(): void
	{
		$this->expectException(ValidationException::class);
		CourseModule::create(1, 'Título', null, 0, 'invalid_status');
	}

	public function test_module_total_duration_calculation(): void
	{
		$module = CourseModule::create(1, 'Módulo Duração');

		$lesson_a = CourseLesson::create(1, 'Aula 1');
		$lesson_a->set_blocks([
			VideoBlock::create_video(1, 'youtube', 'https://youtube.com/watch?v=1', 600)
		]);

		$lesson_b = CourseLesson::create(1, 'Aula 2');
		$lesson_b->set_blocks([
			VideoBlock::create_video(1, 'vimeo', 'https://vimeo.com/2', 400)
		]);

		$module->set_lessons([$lesson_a, $lesson_b]);
		$this->assertSame(1000, $module->get_total_duration_in_seconds());
	}
}
