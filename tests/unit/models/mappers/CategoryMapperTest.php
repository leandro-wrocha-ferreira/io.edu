<?php

namespace tests\unit\models\mappers;

use app\domain\course\Category;
use app\models\dtos\CategoryDatabase;
use app\models\mappers\CategoryMapper;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for CategoryMapper and CategoryDatabase DTO.
 */
class CategoryMapperTest extends TestCase
{
	public function test_to_entity(): void
	{
		$dto = new CategoryDatabase([
			'id' => 1,
			'name' => 'Tecnologia',
			'slug' => 'tecnologia',
			'status' => 'active',
			'created_at' => '2026-10-05 10:00:00',
			'updated_at' => '2026-10-05 11:00:00',
			'deleted_at' => null,
		]);

		$category = CategoryMapper::to_entity($dto);

		$this->assertInstanceOf(Category::class, $category);
		$this->assertSame(1, $category->get_id());
		$this->assertSame('Tecnologia', $category->get_name());
		$this->assertSame('tecnologia', $category->get_slug());
		$this->assertSame('active', $category->get_status());
		$this->assertFalse($category->is_deleted());
	}

	public function test_to_entities(): void
	{
		$rows = [
			[
				'id' => 1,
				'name' => 'Tecnologia',
				'slug' => 'tecnologia',
				'status' => 'active',
			],
			[
				'id' => 2,
				'name' => 'Design',
				'slug' => 'design',
				'status' => 'inactive',
			],
		];

		$entities = CategoryMapper::to_entities($rows);

		$this->assertCount(2, $entities);
		$this->assertSame('Tecnologia', $entities[0]->get_name());
		$this->assertSame('Design', $entities[1]->get_name());
		$this->assertEmpty(CategoryMapper::to_entities([]));
	}

	public function test_to_database_create_and_update(): void
	{
		$category = Category::create('Gestão', 'gestao', 'active', 5);

		$create_data = CategoryMapper::to_database_create($category);
		$this->assertSame('Gestão', $create_data['name']);
		$this->assertSame('gestao', $create_data['slug']);
		$this->assertSame('active', $create_data['status']);
		$this->assertArrayNotHasKey('created_at', $create_data);
		$this->assertArrayNotHasKey('updated_at', $create_data);

		$update_data = CategoryMapper::to_database_update($category);
		$this->assertSame('Gestão', $update_data['name']);
		$this->assertSame('gestao', $update_data['slug']);
		$this->assertSame('active', $update_data['status']);
		$this->assertNull($update_data['deleted_at']);
	}
}
