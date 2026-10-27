<?php

namespace app\infrastructure\events\observers;

use app\infrastructure\events\ModelObserverInterface;

/**
 * Observer that logs model lifecycle changes into the `logs` database table.
 */
class AuditLogObserver implements ModelObserverInterface
{
	/**
	 * Handle model lifecycle events and persist structured audit logs.
	 *
	 * @param string $action Database action (e.g. 'insert', 'update', 'destroy')
	 * @param string $table Database table name
	 * @param int|string|null $identifier Row identifier or condition summary
	 * @param array|null $before Data before operation
	 * @param array|null $after Data after operation
	 * @return void
	 */
	public function handle(string $action, string $table, int|string|null $identifier, ?array $before, ?array $after): void
	{
		if ($table === 'logs' || empty($table)) {
			return;
		}

		if (!function_exists('get_instance')) {
			return;
		}

		$ci_instance =& get_instance();
		if ($ci_instance === null) {
			return;
		}

		$user_id = null;

		if (isset($ci_instance->session)) {
			$user_id = $ci_instance->session->userdata('user_id') ?? null;
		}

		if ($action === 'update' && is_array($before) && is_array($after)) {
			$diff_before = [];
			$diff_after = [];

			$is_multi_row = array_is_list($before) && isset($before[0]) && is_array($before[0]);
			$rows_before = $is_multi_row ? $before : [$before];

			foreach ($rows_before as $row) {
				$row_diff_before = [];
				$row_diff_after = [];

				foreach ($after as $column_name => $new_value) {
					if (is_array($row) && array_key_exists($column_name, $row)) {
						if ($row[$column_name] != $new_value) {
							$row_diff_before[$column_name] = $row[$column_name];
							$row_diff_after[$column_name] = $new_value;
						}
					} else {
						$row_diff_after[$column_name] = $new_value;
					}
				}

				if ($is_multi_row) {
					$diff_before[] = $row_diff_before;
					$diff_after[] = $row_diff_after;
				} else {
					$diff_before = $row_diff_before;
					$diff_after = $row_diff_after;
				}
			}

			$before = $diff_before;
			$after = $diff_after;
		}

		$content = json_encode([
			'action' => $action,
			'before' => $before,
			'after' => $after,
		], JSON_UNESCAPED_UNICODE);

		try {
			if (isset($ci_instance->db)) {
				$ci_instance->db->insert('logs', [
					'user_execute' => $user_id,
					'table' => $table,
					'row' => $identifier,
					'content' => $content,
				]);
			}
		} catch (\Throwable $throwable) {
			if (function_exists('log_message')) {
				log_message('error', 'AuditLogObserver failed to write log: ' . $throwable->getMessage());
			}
		}
	}
}
