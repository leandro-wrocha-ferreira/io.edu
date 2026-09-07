<?php

if (!function_exists('log_message')) {
	function log_message($level, $message) {}
}

if (!class_exists('CI_Model')) {
	require_once __DIR__ . '/../../system/core/Model.php';
}
require_once __DIR__ . '/../../application/core/MY_Model.php';

/**
 * Mock CI Query Builder for testing MY_Model methods.
 */
class MockQueryBuilder
{
	public string $fromTable = '';
	public array $whereConditions = [];
	public ?int $insertedId = 100;
	public array $insertedData = [];
	public array $updatedData = [];
	public ?string $lastQuery = 'SELECT * FROM mock';
	public array $auditLogs = [];

	public function from(string $table): self
	{
		$this->fromTable = $table;
		return $this;
	}

	public function where($key, $value = null): self
	{
		if (is_array($key)) {
			$this->whereConditions = array_merge($this->whereConditions, $key);
		} else {
			$this->whereConditions[$key] = $value;
		}
		return $this;
	}

	public function get(string $table = ''): self
	{
		if ($table !== '') {
			$this->fromTable = $table;
		}
		return $this;
	}

	public function get_compiled_select(string $table = '', bool $reset = true): string
	{
		return "SELECT * FROM {$table}";
	}

	public function query(string $sql): self
	{
		return $this;
	}

	public function result_array(): array
	{
		return [
			['id' => 1, 'name' => 'Item 1'],
			['id' => 2, 'name' => 'Item 2'],
		];
	}

	public function row_array(): ?array
	{
		return ['id' => 1, 'name' => 'Item 1'];
	}

	public function count_all_results(): int
	{
		return 5;
	}

	public function insert(string $table, array $data): bool
	{
		if ($table === 'logs') {
			$this->auditLogs[] = $data;
			return true;
		}

		$this->fromTable = $table;
		$this->insertedData = $data;
		return true;
	}

	public function insert_id(): int
	{
		return $this->insertedId;
	}

	public function insert_batch(string $table, array $data): int
	{
		$this->fromTable = $table;
		return count($data);
	}

	public function update(string $table, array $data): bool
	{
		$this->fromTable = $table;
		$this->updatedData = $data;
		return true;
	}

	public function delete(string $table): bool
	{
		$this->fromTable = $table;
		return true;
	}

	public function last_query(): string
	{
		return $this->lastQuery;
	}
}

/**
 * Concrete test implementation of MY_Model for testing.
 */
class ConcreteTestModel extends MY_Model
{
	public $db;
	public string $table = 'test_table';

	public function __construct()
	{
		$this->db = new MockQueryBuilder();
	}

	public function reset_test_state(): void
	{
		$this->db = new MockQueryBuilder();
	}
}

/**
 * Unit test for MY_Model base class features:
 * - Standard CRUD methods (find_all, find_by_id, count_all, insert, insert_batch, update, delete)
 * - Automatic audit logging to logs table
 * - Entity hydration
 */
class MY_ModelTest extends \PHPUnit\Framework\TestCase
{
	private ConcreteTestModel $model;

	protected function setUp(): void
	{
		$this->model = new ConcreteTestModel();
		$this->model->reset_test_state();
	}

	public function test_find_all(): void
	{
		$res = $this->model->find_all();
		$this->assertCount(2, $res);
		$this->assertEquals(1, $res[0]['id']);
		$this->assertEquals('Item 1', $res[0]['name']);
	}

	public function test_find_by_id(): void
	{
		$res = $this->model->find_by_id(1);
		$this->assertNotNull($res);
		$this->assertEquals(1, $res['id']);
		$this->assertEquals('Item 1', $res['name']);
	}

	public function test_count_all(): void
	{
		$count = $this->model->count_all();
		$this->assertEquals(5, $count);
	}

	public function test_insert_and_insert_batch(): void
	{
		$id = $this->model->insert(['name' => 'New Item']);
		$this->assertEquals(100, $id);
		$this->assertEquals(['name' => 'New Item'], $this->model->db->insertedData);

		$batchCount = $this->model->insert_batch([['name' => 'A'], ['name' => 'B']]);
		$this->assertEquals(2, $batchCount);
	}

	public function test_update(): void
	{
		$res = $this->model->update(['name' => 'Updated'], ['id' => 10]);
		$this->assertTrue($res);
		$this->assertEquals(['name' => 'Updated'], $this->model->db->updatedData);
	}

	public function test_delete(): void
	{
		$res = $this->model->delete(['id' => 50]);
		$this->assertTrue($res);
		$this->assertEquals('test_table', $this->model->db->fromTable);
	}

	public function test_audit_logging_on_write_operations(): void
	{
		$this->model->insert(['name' => 'Logged Item']);
		$this->assertNotEmpty($this->model->db->auditLogs);

		$lastLog = end($this->model->db->auditLogs);
		$this->assertEquals('test_table', $lastLog['table']);
		$this->assertStringContainsString('"action":"insert"', $lastLog['content']);
	}

	public function test_entity_hydration_in_find_by_id_and_find_all(): void
	{
		$model = new EntityTestModel();
		$entity = $model->find_by_id(1);

		$this->assertInstanceOf(DummyEntity::class, $entity);
		$this->assertEquals(1, $entity->id);
		$this->assertEquals('Item 1', $entity->name);

		$entities = $model->find_all();
		$this->assertCount(2, $entities);
		$this->assertInstanceOf(DummyEntity::class, $entities[0]);
		$this->assertInstanceOf(DummyEntity::class, $entities[1]);
	}
}

class DummyEntity
{
	public int $id;
	public string $name;

	public static function from_database(array $row): self
	{
		$entity = new self();
		$entity->id = (int) ($row['id'] ?? 0);
		$entity->name = (string) ($row['name'] ?? '');
		return $entity;
	}
}

class EntityTestModel extends MY_Model
{
	public $db;
	public string $table = 'test_table';
	protected ?string $entity_class = DummyEntity::class;

	public function __construct()
	{
		$this->db = new MockQueryBuilder();
	}
}
