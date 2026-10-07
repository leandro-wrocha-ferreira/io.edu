<?php

namespace tests\unit\domain\course;

use app\domain\course\Category;
use app\domain\course\constants\CategoryStatus;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Category domain entity.
 */
class CategoryTest extends TestCase
{
	public function test_create_category_with_valid_data(): void
	{
		$category = Category::create('Tecnologia', 'tecnologia', CategoryStatus::ACTIVE, 1);

		$this->assertSame(1, $category->get_id());
		$this->assertSame('Tecnologia', $category->get_name());
		$this->assertSame('tecnologia', $category->get_slug());
		$this->assertSame(CategoryStatus::ACTIVE, $category->get_status());
		$this->assertTrue($category->is_active());
		$this->assertFalse($category->is_deleted());
		$this->assertNotNull($category->get_created_at());
	}

	public function test_create_category_auto_generates_slug_when_null_or_empty(): void
	{
		$category = Category::create('Desenvolvimento Web');
		$this->assertSame('desenvolvimento-web', $category->get_slug());

		$category_empty_slug = Category::create('Design & UX', '');
		$this->assertSame('design-ux', $category_empty_slug->get_slug());
	}

	public function test_empty_name_throws_exception(): void
	{
		$this->expectException(InvalidArgumentException::class);
		Category::create('   ', 'tecnologia');
	}

	public function test_invalid_status_throws_exception(): void
	{
		$this->expectException(InvalidArgumentException::class);
		Category::create('Tecnologia', 'tecnologia', 'unknown_status');
	}

	public function test_mutators_and_state_transitions(): void
	{
		$category = Category::create('Design', 'design');

		$category->set_name('Design & UX');
		$this->assertSame('Design & UX', $category->get_name());

		$category->set_slug('design-ux');
		$this->assertSame('design-ux', $category->get_slug());

		$category->deactivate();
		$this->assertFalse($category->is_active());
		$this->assertSame(CategoryStatus::INACTIVE, $category->get_status());

		$category->activate();
		$this->assertTrue($category->is_active());
		$this->assertSame(CategoryStatus::ACTIVE, $category->get_status());

		$category->delete();
		$this->assertTrue($category->is_deleted());
		$this->assertFalse($category->is_active());
		$this->assertNotNull($category->get_deleted_at());
	}
}
