<?php

namespace tests\unit\domain\course;

use app\domain\course\Course;
use app\domain\course\value_objects\CourseAccessPeriod;
use app\domain\course\value_objects\CourseSlug;
use app\domain\course\value_objects\CourseStatus;
use app\domain\course\value_objects\Workload;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Course domain entity.
 */
class CourseTest extends TestCase
{
	public function test_create_course_with_valid_data(): void
	{
		$slug = new CourseSlug('php-8-avancado');
		$access_period = CourseAccessPeriod::limited_time(365);
		$workload = new Workload(120);

		$course = Course::create(
			1,
			'PHP 8 Avançado',
			$slug,
			CourseStatus::DRAFT,
			$access_period,
			$workload,
			'Resumo para vitrine',
			'Ementa completa do curso',
			'https://example.com/cover.jpg',
			3600,
			'Objetivos pedagógicos',
			'Desenvolvedores PHP',
			'PHP básico',
			true,
			10
		);

		$this->assertSame(10, $course->get_id());
		$this->assertSame(1, $course->get_category_id());
		$this->assertSame('PHP 8 Avançado', $course->get_title());
		$this->assertSame('php-8-avancado', (string) $course->get_slug());
		$this->assertTrue($course->is_draft());
		$this->assertFalse($course->is_active());
		$this->assertFalse($course->is_archived());
		$this->assertSame(365, $course->get_access_period()->get_days());
		$this->assertSame(120, $course->get_workload_in_hours());
		$this->assertSame('Resumo para vitrine', $course->get_short_description());
		$this->assertSame('Ementa completa do curso', $course->get_description());
		$this->assertSame('https://example.com/cover.jpg', $course->get_image());
		$this->assertSame(3600, $course->get_duration_in_seconds());
		$this->assertSame('Objetivos pedagógicos', $course->get_objectives());
		$this->assertSame('Desenvolvedores PHP', $course->get_target_audience());
		$this->assertSame('PHP básico', $course->get_requirements());
		$this->assertTrue($course->is_certificate_enabled());
		$this->assertFalse($course->is_deleted());
		$this->assertNotNull($course->get_created_at());
	}

	public function test_invalid_category_id_throws_exception(): void
	{
		$this->expectException(InvalidArgumentException::class);
		Course::create(0, 'Título', 'slug-teste');
	}

	public function test_empty_title_throws_exception(): void
	{
		$this->expectException(InvalidArgumentException::class);
		Course::create(1, '   ', 'slug-teste');
	}

	public function test_status_transitions(): void
	{
		$course = Course::create(1, 'Curso Teste', 'curso-teste');

		$this->assertTrue($course->is_draft());

		$course->publish();
		$this->assertTrue($course->is_active());
		$this->assertFalse($course->is_draft());

		$course->archive();
		$this->assertTrue($course->is_archived());
		$this->assertFalse($course->is_active());

		$course->draft();
		$this->assertTrue($course->is_draft());

		$course->delete();
		$this->assertTrue($course->is_deleted());
		$this->assertFalse($course->is_active());
	}

	public function test_update_details(): void
	{
		$course = Course::create(1, 'Título Antigo', 'slug-antigo');

		$new_slug = new CourseSlug('slug-novo');
		$new_access = CourseAccessPeriod::lifetime();
		$new_workload = new Workload(80);

		$course->update_details(
			2,
			'Título Novo',
			$new_slug,
			$new_access,
			$new_workload,
			'Novo resumo',
			'Nova ementa',
			'nova_imagem.png',
			'Novos objetivos',
			'Novo público',
			'Novos pré-requisitos',
			false
		);

		$this->assertSame(2, $course->get_category_id());
		$this->assertSame('Título Novo', $course->get_title());
		$this->assertSame('slug-novo', (string) $course->get_slug());
		$this->assertTrue($course->get_access_period()->is_lifetime());
		$this->assertSame(80, $course->get_workload_in_hours());
		$this->assertFalse($course->is_certificate_enabled());
	}
}
