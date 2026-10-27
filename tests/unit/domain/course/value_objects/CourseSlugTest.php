<?php

namespace tests\unit\domain\course\value_objects;

use app\domain\course\value_objects\CourseSlug;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for CourseSlug Value Object.
 */
class CourseSlugTest extends TestCase
{
	public function test_valid_slug_instantiation(): void
	{
		$slug = new CourseSlug('php-8-avancado');
		$this->assertSame('php-8-avancado', (string) $slug);
		$this->assertSame('php-8-avancado', $slug->get_value());
	}

	public function test_empty_slug_throws_exception(): void
	{
		$this->expectException(InvalidArgumentException::class);
		new CourseSlug('   ');
	}

	public function test_slugify_converts_accents_special_characters_and_spaces(): void
	{
		$slug = new CourseSlug('Programação Web & Arquitetura Limpa!');
		$this->assertSame('programacao-web-arquitetura-limpa', (string) $slug);
	}

	public function test_from_string_factory_method(): void
	{
		$slug = CourseSlug::from_string('Curso Completo de DDD');
		$this->assertSame('curso-completo-de-ddd', (string) $slug);
	}

	public function test_slug_equality(): void
	{
		$slug1 = new CourseSlug('design-ui');
		$slug2 = new CourseSlug('design-ui');
		$slug3 = new CourseSlug('marketing-digital');

		$this->assertTrue($slug1->equals($slug2));
		$this->assertFalse($slug1->equals($slug3));
	}

	public function test_has_id_placeholder_detection(): void
	{
		$this->assertTrue(CourseSlug::has_id_placeholder('[id]-curso-teste'));
		$this->assertTrue(CourseSlug::has_id_placeholder('id-curso-teste'));
		$this->assertTrue(CourseSlug::has_id_placeholder('[ID]-curso-teste'));
		$this->assertTrue(CourseSlug::has_id_placeholder('ID-curso-teste'));
		$this->assertFalse(CourseSlug::has_id_placeholder('5-curso-teste'));
		$this->assertFalse(CourseSlug::has_id_placeholder('curso-teste'));
	}

	public function test_extract_base_slug(): void
	{
		$this->assertSame('curso-teste', CourseSlug::extract_base_slug('[id]-curso-teste'));
		$this->assertSame('curso-teste', CourseSlug::extract_base_slug('id-curso-teste'));
		$this->assertSame('curso-teste', CourseSlug::extract_base_slug('Curso Teste'));
	}

	public function test_format_with_id(): void
	{
		$slug1 = CourseSlug::format_with_id(5, '[id]-curso-php');
		$this->assertSame('5-curso-php', (string) $slug1);

		$slug2 = CourseSlug::format_with_id(5, 'id-curso-php');
		$this->assertSame('5-curso-php', (string) $slug2);

		$slug3 = CourseSlug::format_with_id(5, '5-curso-php');
		$this->assertSame('5-curso-php', (string) $slug3);

		$slug4 = CourseSlug::format_with_id(12, 'Arquitetura Limpa!');
		$this->assertSame('12-arquitetura-limpa', (string) $slug4);

		$slug5 = CourseSlug::format_with_id(7, '');
		$this->assertSame('7-curso', (string) $slug5);
	}
}
