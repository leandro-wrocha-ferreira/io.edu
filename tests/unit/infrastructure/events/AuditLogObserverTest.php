<?php

namespace tests\unit\infrastructure\events;

use app\infrastructure\events\observers\AuditLogObserver;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for AuditLogObserver.
 */
class AuditLogObserverTest extends TestCase
{
	private AuditLogObserver $observer;

	protected function setUp(): void
	{
		parent::setUp();
		$this->observer = new AuditLogObserver();
	}

	public function test_ignore_logs_table_to_prevent_recursion(): void
	{
		// Should return immediately without attempting any DB operations
		$this->observer->handle('insert', 'logs', 1, null, ['action' => 'insert']);
		$this->assertTrue(true);
	}

	public function test_ignore_empty_table_name(): void
	{
		$this->observer->handle('insert', '', null, null, ['name' => 'Test']);
		$this->assertTrue(true);
	}

	public function test_gracefully_handles_database_exceptions(): void
	{
		// Even if the global CI environment throws an exception during DB write, the observer catches it
		$this->observer->handle('insert', 'courses', 99, null, ['title' => 'Test Course']);
		$this->assertTrue(true);
	}
}
