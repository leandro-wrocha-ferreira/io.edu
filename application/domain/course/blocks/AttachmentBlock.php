<?php

namespace app\domain\course\blocks;

use app\domain\course\constants\BlockType;
use app\domain\course\LessonBlock;
use app\domain\exceptions\ValidationException;
use DateTime;

/**
 * Domain entity representing a downloadable file attachment / PDF block.
 */
class AttachmentBlock extends LessonBlock
{
	/**
	 * Display file name.
	 *
	 * @var string
	 */
	private string $filename;

	/**
	 * File storage path or download URL.
	 *
	 * @var string
	 */
	private string $filepath;

	/**
	 * File size in bytes.
	 *
	 * @var int
	 */
	private int $file_size = 0;

	/**
	 * MIME type or file extension.
	 *
	 * @var string
	 */
	private string $file_type = '';

	/**
	 * Minimum reading / inspection time in minutes required before advancing.
	 *
	 * @var int
	 */
	private int $min_reading_time_in_minutes = 0;

	/**
	 * Factory method for AttachmentBlock.
	 *
	 * @param int $lesson_id Parent lesson ID
	 * @param string $filename File title/name
	 * @param string $filepath File path or URL
	 * @param int $min_reading_time_in_minutes Minimum required reading time in minutes
	 * @param int $file_size File size in bytes
	 * @param string $file_type MIME type or extension
	 * @param int $sort_order Sequential sort order
	 * @param bool $is_required Mandatory completion flag
	 * @param int|null $id Block ID
	 * @param DateTime|null $created_at Creation timestamp
	 * @param DateTime|null $updated_at Update timestamp
	 * @param DateTime|null $deleted_at Deletion timestamp
	 * @return self
	 */
	public static function create_attachment(
		int $lesson_id,
		string $filename,
		string $filepath,
		int $min_reading_time_in_minutes = 0,
		int $file_size = 0,
		string $file_type = '',
		int $sort_order = 0,
		bool $is_required = true,
		?int $id = null,
		?DateTime $created_at = null,
		?DateTime $updated_at = null,
		?DateTime $deleted_at = null
	): self
	{
		$trimmed_filename = trim($filename);
		$trimmed_filepath = trim($filepath);

		if ($trimmed_filename === '') {
			throw new ValidationException("Attachment filename cannot be empty");
		}
		if ($trimmed_filepath === '') {
			throw new ValidationException("Attachment filepath cannot be empty");
		}

		$block = new self();
		$block->id = $id;
		$block->lesson_id = $lesson_id;
		$block->type = BlockType::ATTACHMENT;
		$block->filename = $trimmed_filename;
		$block->filepath = $trimmed_filepath;
		$block->file_size = max(0, $file_size);
		$block->file_type = trim($file_type);
		$block->min_reading_time_in_minutes = max(0, $min_reading_time_in_minutes);
		$block->sort_order = max(0, $sort_order);
		$block->is_required = $is_required;
		$block->created_at = $created_at ?? new DateTime();
		$block->updated_at = $updated_at ?? ($id === null ? new DateTime() : null);
		$block->deleted_at = $deleted_at;

		return $block;
	}

	/**
	 * Update attachment block parameters.
	 *
	 * @param string $filename
	 * @param string $filepath
	 * @param int $min_reading_time_in_minutes
	 * @param int $file_size
	 * @param string $file_type
	 * @param bool $is_required
	 * @return void
	 * @throws ValidationException When filename or filepath is empty
	 */
	public function update_attachment(
		string $filename,
		string $filepath,
		int $min_reading_time_in_minutes = 0,
		int $file_size = 0,
		string $file_type = '',
		bool $is_required = true
	): void
	{
		$trimmed_filename = trim($filename);
		$trimmed_filepath = trim($filepath);

		if ($trimmed_filename === '') {
			throw new ValidationException("Attachment filename cannot be empty");
		}
		if ($trimmed_filepath === '') {
			throw new ValidationException("Attachment filepath cannot be empty");
		}

		$this->filename = $trimmed_filename;
		$this->filepath = $trimmed_filepath;
		$this->min_reading_time_in_minutes = max(0, $min_reading_time_in_minutes);
		$this->file_size = max(0, $file_size);
		$this->file_type = trim($file_type);
		$this->is_required = $is_required;
		$this->updated_at = new DateTime();
	}

	public function get_filename(): string
	{
		return $this->filename;
	}

	public function get_filepath(): string
	{
		return $this->filepath;
	}

	public function get_file_size(): int
	{
		return $this->file_size;
	}

	public function get_file_type(): string
	{
		return $this->file_type;
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
			'filename' => $this->filename,
			'filepath' => $this->filepath,
			'file_size' => $this->file_size,
			'file_type' => $this->file_type,
			'min_reading_time_in_minutes' => $this->min_reading_time_in_minutes,
		];
	}
}
