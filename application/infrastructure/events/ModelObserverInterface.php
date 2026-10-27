<?php

namespace app\infrastructure\events;

/**
 * Interface for database model lifecycle observers.
 */
interface ModelObserverInterface
{
	/**
	 * Handle a model lifecycle event.
	 *
	 * @param string $action Database action (e.g. 'insert', 'update', 'destroy')
	 * @param string $table Database table name
	 * @param int|string|null $identifier Row identifier or condition summary
	 * @param array|null $before Data state prior to operation
	 * @param array|null $after Data state following operation
	 * @return void
	 */
	public function handle(string $action, string $table, int|string|null $identifier, ?array $before, ?array $after): void;
}
