<?php
namespace app\domain\education\video;

/**
 * Repository interface for VideoProvider entity persistence.
 */
interface VideoProviderRepositoryInterface
{
	/**
	 * Find a provider by ID.
	 *
	 * @param int|string $id
	 * @return VideoProvider|null
	 */
	public function find_by_id($id): ?VideoProvider;

	/**
	 * Get all configured video providers.
	 *
	 * @return VideoProvider[]
	 */
	public function find_all(): array;

	/**
	 * Save a video provider (insert or update).
	 *
	 * @param VideoProvider $provider
	 * @return int Provider ID
	 */
	public function save(VideoProvider $provider): int;

	/**
	 * Delete video providers matching specified conditions.
	 *
	 * @param array $where Filter conditions (e.g. ['id' => $id])
	 * @return bool
	 */
	public function delete(array $where): bool;
}
