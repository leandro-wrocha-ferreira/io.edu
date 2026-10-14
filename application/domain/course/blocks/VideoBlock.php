<?php

namespace app\domain\course\blocks;

use app\domain\course\constants\BlockType;
use app\domain\course\LessonBlock;
use app\domain\exceptions\ValidationException;
use DateTime;

/**
 * Domain entity representing a Video content block.
 */
class VideoBlock extends LessonBlock
{
	/**
	 * Video provider ('youtube', 'vimeo', 'bunny', 'custom').
	 *
	 * @var string
	 */
	private string $provider;

	/**
	 * Video URL, embed ID or resource path.
	 *
	 * @var string
	 */
	private string $url;

	/**
	 * Video duration in seconds.
	 *
	 * @var int
	 */
	private int $duration_in_seconds = 0;

	/**
	 * Factory method for VideoBlock.
	 *
	 * @param int $lesson_id Parent lesson ID
	 * @param string $provider Video provider
	 * @param string $url Video URL / embed code
	 * @param int $duration_in_seconds Video length in seconds
	 * @param int $sort_order Sequential sort order
	 * @param bool $is_required Mandatory completion flag
	 * @param int|null $id Block ID
	 * @param DateTime|null $created_at Creation timestamp
	 * @param DateTime|null $updated_at Update timestamp
	 * @param DateTime|null $deleted_at Deletion timestamp
	 * @return self
	 */
	public static function create_video(
		int $lesson_id,
		string $provider,
		string $url,
		int $duration_in_seconds = 0,
		int $sort_order = 0,
		bool $is_required = true,
		?int $id = null,
		?DateTime $created_at = null,
		?DateTime $updated_at = null,
		?DateTime $deleted_at = null
	): self
	{
		$trimmed_url = trim($url);
		if ($trimmed_url === '') {
			throw new ValidationException("Video URL cannot be empty");
		}

		$block = new self();
		$block->id = $id;
		$block->lesson_id = $lesson_id;
		$block->type = BlockType::VIDEO;
		$block->provider = trim($provider) !== '' ? trim($provider) : 'youtube';
		$block->url = $trimmed_url;
		$block->duration_in_seconds = max(0, $duration_in_seconds);
		$block->sort_order = max(0, $sort_order);
		$block->is_required = $is_required;
		$block->created_at = $created_at ?? new DateTime();
		$block->updated_at = $updated_at ?? ($id === null ? new DateTime() : null);
		$block->deleted_at = $deleted_at;

		return $block;
	}

	/**
	 * Update video block parameters.
	 *
	 * @param string $provider
	 * @param string $url
	 * @param int $duration_in_seconds
	 * @param bool $is_required
	 * @return void
	 * @throws ValidationException When URL is empty
	 */
	public function update_video(
		string $provider,
		string $url,
		int $duration_in_seconds = 0,
		bool $is_required = true
	): void
	{
		$trimmed_url = trim($url);
		if ($trimmed_url === '') {
			throw new ValidationException("Video URL cannot be empty");
		}

		$this->provider = trim($provider) !== '' ? trim($provider) : 'youtube';
		$this->url = $trimmed_url;
		$this->duration_in_seconds = max(0, $duration_in_seconds);
		$this->is_required = $is_required;
		$this->updated_at = new DateTime();
	}

	public function get_provider(): string
	{
		return $this->provider;
	}

	public function get_url(): string
	{
		return $this->url;
	}

	public function get_duration_in_seconds(): int
	{
		return $this->duration_in_seconds;
	}

	/**
	 * Get serializable array payload.
	 *
	 * @return array<string, mixed>
	 */
	public function get_payload(): array
	{
		return [
			'provider' => $this->provider,
			'url' => $this->url,
			'duration_in_seconds' => $this->duration_in_seconds,
		];
	}
}
