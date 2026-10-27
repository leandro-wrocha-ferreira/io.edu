<?php

namespace tests\unit\mocks\repositories;

use app\domain\exceptions\ConflictException;
use app\domain\exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for MockTransactionManager verifying TransactionInterface contract behaviors.
 */
class MockTransactionManagerTest extends TestCase
{
	private MockTransactionManager $transaction_manager;

	protected function setUp(): void
	{
		parent::setUp();
		$this->transaction_manager = new MockTransactionManager();
	}

	public function test_successful_run_commits_and_returns_result(): void
	{
		$result = $this->transaction_manager->run(function () {
			return 'success_payload';
		});

		$this->assertSame('success_payload', $result);
		$this->assertTrue($this->transaction_manager->was_committed());
		$this->assertFalse($this->transaction_manager->was_rolled_back());
		$this->assertSame(0, $this->transaction_manager->get_depth());
	}

	public function test_run_with_app_exception_triggers_rollback_and_rethrows(): void
	{
		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Validation error inside transaction');

		$this->transaction_manager->run(function () {
			throw new ValidationException('Validation error inside transaction');
		});

		$this->assertTrue($this->transaction_manager->was_rolled_back());
		$this->assertFalse($this->transaction_manager->was_committed());
		$this->assertSame(0, $this->transaction_manager->get_depth());
	}

	public function test_run_with_generic_exception_triggers_rollback_and_wraps_in_conflict_exception(): void
	{
		$this->expectException(ConflictException::class);
		$this->expectExceptionMessage('Database transaction failed.');

		$this->transaction_manager->run(function () {
			throw new \RuntimeException('Low-level database connection lost');
		});

		$this->assertTrue($this->transaction_manager->was_rolled_back());
		$this->assertSame(0, $this->transaction_manager->get_depth());
	}

	public function test_run_with_explicit_false_return_triggers_rollback_and_throws_conflict_exception(): void
	{
		$this->expectException(ConflictException::class);
		$this->expectExceptionMessage('Database transaction failed.');

		$this->transaction_manager->run(function () {
			// Simulates a persistence operation that failed and returned false
			return false;
		});

		$this->assertTrue($this->transaction_manager->was_rolled_back());
		$this->assertSame(0, $this->transaction_manager->get_depth());
	}

	public function test_run_with_failed_status_triggers_rollback_and_throws_conflict_exception(): void
	{
		$this->expectException(ConflictException::class);
		$this->expectExceptionMessage('Database transaction failed.');

		$this->transaction_manager->run(function () {
			$this->transaction_manager->set_status(false);
			return 'done';
		});

		$this->assertTrue($this->transaction_manager->was_rolled_back());
		$this->assertSame(0, $this->transaction_manager->get_depth());
	}

	public function test_nested_transactions_both_succeed(): void
	{
		$result = $this->transaction_manager->run(function () {
			$nested_result = $this->transaction_manager->run(function () {
				return 'nested_success';
			});

			return $nested_result . '_outer';
		});

		$this->assertSame('nested_success_outer', $result);
		$this->assertTrue($this->transaction_manager->was_committed());
		$this->assertSame(0, $this->transaction_manager->get_depth());
	}

	public function test_nested_transaction_failure_aborts_entire_transaction(): void
	{
		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Nested failure');

		$this->transaction_manager->run(function () {
			$this->transaction_manager->run(function () {
				throw new ValidationException('Nested failure');
			});

			return 'never_reached';
		});

		$this->assertTrue($this->transaction_manager->was_rolled_back());
		$this->assertSame(0, $this->transaction_manager->get_depth());
	}
}
