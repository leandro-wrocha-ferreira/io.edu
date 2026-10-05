<?php

namespace tests\unit\models\mappers;

use app\domain\course\Course;
use app\domain\course\value_objects\CourseAccessPeriod;
use app\domain\course\value_objects\CourseSlug;
use app\domain\course\value_objects\CourseStatus;
use app\domain\course\value_objects\Workload;
use app\models\dtos\CourseDatabase;
use app\models\mappers\CourseMapper;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for CourseMapper and CourseDatabase DTO.
 */
class CourseMapperTest extends TestCase
{
	public function test_to_entity(): void
	{
		$dto = new CourseDatabase([
			'id' => 10,
			'category_id' => 2,
			'title' => 'Curso de Clean Code',
			'slug' => 'curso-clean-code',
			'short_description' => 'Resumo curto',
			'description' => 'Ementa do curso',
			'image' => 'https://img.png',
			'status' => 'active',
			'workload_in_hours' => 60,
			'duration_in_seconds' => 7200,
			'objectives' => 'Boas práticas',
			'target_audience' => 'Devs',
			'requirements' => 'Lógica',
			'access_period_type' => 'limited_time',
			'access_days' => 365,
			'certificate_enabled' => 1,
			'created_at' => '2026-10-05 10:00:00',
			'updated_at' => '2026-10-05 11:00:00',
			'deleted_at' => null,
			'category_name' => 'Tecnologia',
		]);

		$course = CourseMapper::to_entity($dto);

		$this->assertInstanceOf(Course::class, $course);
		$this->assertSame(10, $course->get_id());
		$this->assertSame(2, $course->get_category_id());
		$this->assertSame('Curso de Clean Code', $course->get_title());
		$this->assertSame('curso-clean-code', (string) $course->get_slug());
		$this->assertSame('Resumo curto', $course->get_short_description());
		$this->assertSame('Ementa do curso', $course->get_description());
		$this->assertSame('https://img.png', $course->get_image());
		$this->assertTrue($course->is_active());
		$this->assertSame(60, $course->get_workload_in_hours());
		$this->assertSame(7200, $course->get_duration_in_seconds());
		$this->assertSame('Boas práticas', $course->get_objectives());
		$this->assertSame('Devs', $course->get_target_audience());
		$this->assertSame('Lógica', $course->get_requirements());
		$this->assertSame(365, $course->get_access_period()->get_days());
		$this->assertTrue($course->is_certificate_enabled());
		$this->assertSame('Tecnologia', $course->get_category_name());
	}

	public function test_to_entities(): void
	{
		$rows = [
			[
				'id' => 1,
				'category_id' => 1,
				'title' => 'Curso 1',
				'slug' => 'curso-1',
				'status' => 'draft',
				'access_period_type' => 'limited_time',
				'access_days' => 365,
			],
			[
				'id' => 2,
				'category_id' => 1,
				'title' => 'Curso 2',
				'slug' => 'curso-2',
				'status' => 'active',
				'access_period_type' => 'lifetime',
			],
		];

		$entities = CourseMapper::to_entities($rows);

		$this->assertCount(2, $entities);
		$this->assertSame('Curso 1', $entities[0]->get_title());
		$this->assertSame('Curso 2', $entities[1]->get_title());
		$this->assertEmpty(CourseMapper::to_entities([]));
	}

	public function test_to_database_create_and_update(): void
	{
		$course = Course::create(
			1,
			'DDD em PHP',
			new CourseSlug('ddd-em-php'),
			CourseStatus::active(),
			CourseAccessPeriod::lifetime(),
			new Workload(40),
			'Resumo',
			'Descrição',
			'cover.jpg',
			1000,
			'Objetivo',
			'Público',
			'Requisito',
			true,
			7
		);

		$create_data = CourseMapper::to_database_create($course);
		$this->assertSame(1, $create_data['category_id']);
		$this->assertSame('DDD em PHP', $create_data['title']);
		$this->assertSame('ddd-em-php', $create_data['slug']);
		$this->assertSame('active', $create_data['status']);
		$this->assertSame('lifetime', $create_data['access_period_type']);
		$this->assertNull($create_data['access_days']);
		$this->assertSame(40, $create_data['workload_in_hours']);
		$this->assertSame(1, $create_data['certificate_enabled']);
		$this->assertArrayNotHasKey('created_at', $create_data);
		$this->assertArrayNotHasKey('updated_at', $create_data);

		$update_data = CourseMapper::to_database_update($course);
		$this->assertSame('DDD em PHP', $update_data['title']);
		$this->assertNull($update_data['deleted_at']);
	}
}
