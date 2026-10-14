<?php

namespace app\domain\course\blocks;

use app\domain\course\constants\BlockType;
use app\domain\course\LessonBlock;
use app\domain\exceptions\ValidationException;
use DateTime;

/**
 * Domain entity representing a rich Text / Article content block.
 */
class TextBlock extends LessonBlock
{
	/**
	 * Markdown / HTML text content.
	 *
	 * @var string
	 */
	private string $content;

	/**
	 * Minimum reading / permanence time in minutes required before advancing.
	 *
	 * @var int
	 */
	private int $min_reading_time_in_minutes = 0;

	/**
	 * Factory method for TextBlock.
	 *
	 * @param int $lesson_id Parent lesson ID
	 * @param string $content Text body
	 * @param int $min_reading_time_in_minutes Minimum required reading time in minutes
	 * @param int $sort_order Sequential sort order
	 * @param bool $is_required Mandatory completion flag
	 * @param int|null $id Block ID
	 * @param DateTime|null $created_at Creation timestamp
	 * @param DateTime|null $updated_at Update timestamp
	 * @param DateTime|null $deleted_at Deletion timestamp
	 * @return self
	 */
	public static function create_text(
		int $lesson_id,
		string $content,
		int $min_reading_time_in_minutes = 0,
		int $sort_order = 0,
		bool $is_required = true,
		?int $id = null,
		?DateTime $created_at = null,
		?DateTime $updated_at = null,
		?DateTime $deleted_at = null
	): self
	{
		$trimmed_content = trim($content);
		if ($trimmed_content === '') {
			throw new ValidationException("Text block content cannot be empty");
		}

		$block = new self();
		$block->id = $id;
		$block->lesson_id = $lesson_id;
		$block->type = BlockType::TEXT;
		$block->content = $trimmed_content;
		$block->min_reading_time_in_minutes = max(0, $min_reading_time_in_minutes);
		$block->sort_order = max(0, $sort_order);
		$block->is_required = $is_required;
		$block->created_at = $created_at ?? new DateTime();
		$block->updated_at = $updated_at ?? ($id === null ? new DateTime() : null);
		$block->deleted_at = $deleted_at;

		return $block;
	}

	/**
	 * Update text block content and parameters.
	 *
	 * @param string $content
	 * @param int $min_reading_time_in_minutes
	 * @param bool $is_required
	 * @return void
	 * @throws ValidationException When content is empty
	 */
	public function update_text(
		string $content,
		int $min_reading_time_in_minutes = 0,
		bool $is_required = true
	): void
	{
		$trimmed_content = trim($content);
		if ($trimmed_content === '') {
			throw new ValidationException("Text block content cannot be empty");
		}

		$this->content = $trimmed_content;
		$this->min_reading_time_in_minutes = max(0, $min_reading_time_in_minutes);
		$this->is_required = $is_required;
		$this->updated_at = new DateTime();
	}

	public function get_content(): string
	{
		return $this->content;
	}

	public function get_min_reading_time_in_minutes(): int
	{
		return $this->min_reading_time_in_minutes;
	}

	public function get_min_reading_time_in_seconds(): int
	{
		return $this->min_reading_time_in_minutes * 60;
	}

	/**
	 * Get serializable array payload.
	 *
	 * @return array<string, mixed>
	 */
	public function get_payload(): array
	{
		return [
			'content' => $this->content,
			'min_reading_time_in_minutes' => $this->min_reading_time_in_minutes,
		];
	}
}
