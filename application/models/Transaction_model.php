<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\exceptions\AppException;
use app\domain\exceptions\ConflictException;
use app\infrastructure\database\TransactionInterface;

/**
 * Transaction model implementing TransactionInterface.
 *
 * Coordinates atomic database transactions using CodeIgniter 3 query builder transaction engine.
 * Guarantees that all operations executed within the transaction runner share the primary
 * database connection ($this->db singleton) and properly handles nested transactional boundaries.
 */
class Transaction_model extends CI_Model implements TransactionInterface
{
	/**
	 * Internal nesting depth tracker.
	 *
	 * @var int
	 */
	private int $depth = 0;

	/**
	 * Flag indicating whether a transaction at any nesting level has failed or rolled back.
	 *
	 * @var bool
	 */
	private bool $has_failed = false;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Begin a database transaction.
	 *
	 * Increments internal nesting depth. If this is the outermost transaction,
	 * resets the failure flag and starts the physical database transaction.
	 *
	 * @return void
	 */
	public function begin(): void
	{
		if ($this->depth === 0) {
			$this->has_failed = false;
		}

		$this->depth++;
		$this->db->trans_begin();
	}

	/**
	 * Commit the active database transaction.
	 *
	 * If the transaction has previously failed at any nesting level,
	 * aborts the commit, triggers a rollback, and throws a ConflictException.
	 *
	 * @return void
	 * @throws ConflictException When committing a failed transaction
	 */
	public function commit(): void
	{
		if ($this->has_failed || !$this->status()) {
			$this->rollback();
			throw new ConflictException('Database transaction failed.');
		}

		$this->depth = max(0, $this->depth - 1);
		$this->db->trans_commit();
	}

	/**
	 * Rollback the active database transaction.
	 *
	 * Sets the failure flag to true to prevent outer transactions from committing,
	 * and decrements internal nesting depth while executing database rollback.
	 *
	 * @return void
	 */
	public function rollback(): void
	{
		$this->has_failed = true;
		$this->depth = max(0, $this->depth - 1);
		$this->db->trans_rollback();

		if ($this->depth === 0) {
			$this->has_failed = false;
		}
	}

	/**
	 * Check the status of the current database transaction.
	 *
	 * Returns true only if CI3 trans_status is true, no DB error has been registered,
	 * and no nested rollback flag has been raised.
	 *
	 * @return bool True if transaction has not failed, false otherwise
	 */
	public function status(): bool
	{
		$db_error = $this->db->error();
		$has_error = !empty($db_error['code']) && $db_error['code'] !== 0;

		return $this->db->trans_status() && !$has_error && !$this->has_failed;
	}

	/**
	 * Get the current nesting depth.
	 *
	 * @return int
	 */
	public function get_depth(): int
	{
		return $this->depth;
	}

	/**
	 * Execute an operation callable inside an atomic transaction with automatic rollback on error.
	 *
	 * Guarantees rollback if:
	 * 1. The callable throws any Throwable
	 * 2. The callable explicitly returns false (indicating a failed persistence operation)
	 * 3. The transaction status fails or a database query error is registered
	 *
	 * @param callable $operation Function to execute within transaction
	 * @return mixed Result of the callable operation
	 * @throws AppException Re-throws AppException or wraps unexpected throwables
	 */
	public function run(callable $operation): mixed
	{
		$this->begin();

		try {
			$result = $operation();

			if ($result === false || $this->status() === false) {
				$this->rollback();
				throw new ConflictException('Database transaction failed.');
			}

			$this->commit();

			return $result;
		} catch (\Throwable $throwable) {
			$this->rollback();

			if ($throwable instanceof AppException) {
				throw $throwable;
			}

			throw new ConflictException('Database transaction failed.', [], $throwable);
		}
	}
}
