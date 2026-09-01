<?php

if (!function_exists('log_message')) {
	function log_message($level, $message) {}
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

	public function get(): self
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
	public bool $scope_ran = false;
	public bool $other_scope_ran = false;
	public ?array $audit_log = null;
	public ?array $update_audit = null;
	public mixed $delete_audit = null;

	public string $table = 'test_table';
	public array $before_get = ['scope_first', 'scope_second'];
	public array $after_create = ['log_creation'];
	public array $after_update = ['log_update'];
	public array $after_delete = ['log_delete'];

	public function __construct()
	{
		$this->db = new MockQueryBuilder();
	}

	protected function scope_first(): void
	{
		$this->scope_ran = true;
	}

	protected function scope_second(): void
	{
		$this->other_scope_ran = true;
	}

	protected function log_creation($insert_id, array $data): void
	{
		$this->audit_log = ['id' => $insert_id, 'data' => $data];
	}

	protected function log_update($result, array $data = [], $id = null): void
	{
		$this->update_audit = ['id' => $id, 'data' => $data, 'result' => $result];
	}

	protected function log_delete($result, array $data = [], $id = null): void
	{
		$this->delete_audit = $id;
	}

	public function reset_test_state(): void
	{
		$this->scope_ran = false;
		$this->other_scope_ran = false;
		$this->audit_log = null;
		$this->update_audit = null;
		$this->delete_audit = null;
		$this->db = new MockQueryBuilder();
	}
}

/**
 * Unit test for MY_Model base class features:
 * - Automatic query scoping and lifecycle callbacks
 * - Scope bypass mechanisms
 * - Standard CRUD methods (get_all, find_all, get_by_id, find_by_id, get_by, count_all, count_by, insert, insert_batch, update_record, update_by, delete_record, delete_by)
 */
class MY_ModelTest extends \PHPUnit\Framework\TestCase
{
	private ConcreteTestModel $model;

	protected function setUp(): void
	{
		$this->model = new ConcreteTestModel();
		$this->model->reset_test_state();
	}

	public function test_get_all_and_find_all(): void
	{
		$res1 = $this->model->get_all();
		$this->assertCount(2, $res1);
		$this->assertTrue($this->model->scope_ran);

		$this->model->reset_test_state();
		$res2 = $this->model->find_all();
		$this->assertCount(2, $res2);
		$this->assertTrue($this->model->scope_ran);
	}

	public function test_get_by_id_and_find_by_id(): void
	{
		$res1 = $this->model->get_by_id(1);
		$this->assertNotNull($res1);
		$this->assertEquals(1, $res1['id']);

		$this->model->reset_test_state();
		$res2 = $this->model->find_by_id(1);
		$this->assertNotNull($res2);
		$this->assertEquals(1, $res2['id']);
	}

	public function test_get_by(): void
	{
		$row = $this->model->get_by(['name' => 'Item 1']);
		$this->assertNotNull($row);
		$this->assertEquals('Item 1', $row['name']);
	}

	public function test_count_all_and_count_by(): void
	{
		$count1 = $this->model->count_all();
		$this->assertEquals(5, $count1);

		$count2 = $this->model->count_by(['active' => 1]);
		$this->assertEquals(5, $count2);
	}

	public function test_insert_and_insert_batch(): void
	{
		$id = $this->model->insert(['name' => 'New Item']);
		$this->assertEquals(100, $id);
		$this->assertNotNull($this->model->audit_log);
		$this->assertEquals(['name' => 'New Item'], $this->model->audit_log['data']);

		$batchCount = $this->model->insert_batch([['name' => 'A'], ['name' => 'B']]);
		$this->assertEquals(2, $batchCount);
	}

	public function test_update_record_and_update_by(): void
	{
		$res1 = $this->model->update_record(10, ['name' => 'Updated']);
		$this->assertTrue($res1);
		$this->assertNotNull($this->model->update_audit);
		$this->assertEquals(10, $this->model->update_audit['id']);

		$res2 = $this->model->update_by(['id' => 10], ['name' => 'Updated By']);
		$this->assertTrue($res2);
	}

	public function test_delete_record_and_delete_by(): void
	{
		$res1 = $this->model->delete_record(50);
		$this->assertTrue($res1);
		$this->assertEquals(50, $this->model->delete_audit);

		$res2 = $this->model->delete_by(['id' => 50]);
		$this->assertTrue($res2);
	}

	public function test_without_scope_bypasses_specified_scope(): void
	{
		$this->model->without_scope('scope_first');
		$this->model->get_all();

		$this->assertFalse($this->model->scope_ran);
		$this->assertTrue($this->model->other_scope_ran);

		// Scopes reset automatically
		$this->model->reset_test_state();
		$this->model->get_all();
		$this->assertTrue($this->model->scope_ran);
	}

	public function test_without_global_scopes_bypasses_all_scopes(): void
	{
		$this->model->without_global_scopes();
		$this->model->get_all();

		$this->assertFalse($this->model->scope_ran);
		$this->assertFalse($this->model->other_scope_ran);

		// Scopes reset automatically
		$this->model->reset_test_state();
		$this->model->get_all();
		$this->assertTrue($this->model->scope_ran);
		$this->assertTrue($this->model->other_scope_ran);
	}

	public function test_with_global_scopes_reenables(): void
	{
		$this->model->without_global_scopes();
		$this->model->with_global_scopes();
		$this->model->get_all();

		$this->assertTrue($this->model->scope_ran);
		$this->assertTrue($this->model->other_scope_ran);
	}
}
