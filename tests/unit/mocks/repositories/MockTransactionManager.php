<?php

namespace tests\unit\mocks\repositories;

use app\domain\exceptions\AppException;
use app\domain\exceptions\ConflictException;
use app\infrastructure\database\TransactionInterface;

/**
 * Shared mock transaction for unit testing atomic transactional flows.
 */
class MockTransactionManager implements TransactionInterface
{
	private int $depth = 0;
	private bool $was_committed = false;
	private bool $was_rolled_back = false;
	private bool $status = true;
	private bool $has_failed = false;

	public function begin(): void
	{
		if ($this->depth === 0) {
			$this->has_failed = false;
		}

		$this->depth++;
	}

	public function commit(): void
	{
		if ($this->has_failed || !$this->status()) {
			$this->rollback();
			throw new ConflictException('Database transaction failed.');
		}

		$this->depth = max(0, $this->depth - 1);
		$this->was_committed = true;
	}

	public function rollback(): void
	{
		$this->has_failed = true;
		$this->depth = max(0, $this->depth - 1);
		$this->was_rolled_back = true;

		if ($this->depth === 0) {
			$this->has_failed = false;
		}
	}

	public function status(): bool
	{
		return $this->status && !$this->has_failed;
	}

	public function set_status(bool $status): void
	{
		$this->status = $status;
	}

	public function get_depth(): int
	{
		return $this->depth;
	}

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

	public function was_committed(): bool
	{
		return $this->was_committed;
	}

	public function was_rolled_back(): bool
	{
		return $this->was_rolled_back;
	}
}
