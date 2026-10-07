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
}
