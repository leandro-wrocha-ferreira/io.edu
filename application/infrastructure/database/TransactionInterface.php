<?php

namespace app\infrastructure\database;

/**
 * Contract for database atomic transaction management.
 *
 * Coordinates atomic database transactions across use cases. All operations executed
 * within the transaction runner share the primary database connection ($this->db singleton).
 */
interface TransactionInterface
{
	/**
	 * Begin a database transaction.
	 *
	 * @return void
	 */
	public function begin(): void;

	/**
	 * Commit the active database transaction.
	 *
	 * @return void
	 */
	public function commit(): void;

	/**
	 * Rollback the active database transaction.
	 *
	 * @return void
	 */
	public function rollback(): void;

	/**
	 * Check the status of the current database transaction.
	 *
	 * @return bool True if transaction has not failed, false otherwise
	 */
	public function status(): bool;

	/**
	 * Get the current nesting depth of the transaction.
	 *
	 * @return int
	 */
	public function get_depth(): int;

	/**
	 * Execute an operation callable inside an atomic transaction with automatic rollback on error.
	 *
	 * Guarantees rollback if:
	 * 1. The callable throws any Throwable
	 * 2. The callable explicitly returns false (failed persistence)
	 * 3. The transaction status fails or a database query error is registered
	 *
	 * @param callable $operation Function to execute within transaction
	 * @return mixed Result of the callable operation
	 */
	public function run(callable $operation): mixed;
}
