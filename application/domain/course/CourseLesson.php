<?php

namespace app\domain\course;

use app\domain\course\constants\ContentStatus;
use app\domain\exceptions\ValidationException;
use DateTime;

/**
 * Domain entity representing an educational Course Lesson.
 */
final class CourseLesson
{
	/**
	 * Unique identifier.
	 *
	 * @var int|null
	 */
	private ?int $id = null;

	/**
	 * Parent module ID.
	 *
	 * @var int
	 */
	private int $module_id;

	/**
	 * Lesson title.
	 *
	 * @var string
	 */
	private string $title;

	/**
	 * Lesson description or summary.
	 *
	 * @var string|null
	 */
	private ?string $description = null;

	/**
	 * Sequential sort order within the module.
	 *
	 * @var int
	 */
	private int $sort_order = 0;

	/**
	 * Publication status ('draft', 'published').
	 *
	 * @var string
	 */
	private string $status = ContentStatus::DRAFT;

	/**
	 * Creation timestamp.
	 *
	 * @var DateTime|null
	 */
	private ?DateTime $created_at = null;

	/**
	 * Update timestamp.
	 *
	 * @var DateTime|null
	 */
	private ?DateTime $updated_at = null;

	/**
	 * Soft deletion timestamp.
	 *
	 * @var DateTime|null
	 */
	private ?DateTime $deleted_at = null;

	/**
	 * Lesson content blocks (optional collection projection).
	 *
	 * @var array<LessonBlock>
	 */
	private array $blocks = [];

	/**
	 * Private constructor.
	 */
	private function __construct()
	{
	}

	/**
	 * Unified factory method to create a CourseLesson domain entity.
	 *
	 * @param int $module_id Parent module ID
	 * @param string $title Lesson title
	 * @param string|null $description Optional description
	 * @param int $sort_order Sequential sort order
	 * @param string $status Status ('draft', 'published')
	 * @param int|null $id Lesson ID
	 * @param DateTime|null $created_at Creation timestamp
	 * @param DateTime|null $updated_at Update timestamp
	 * @param DateTime|null $deleted_at Deletion timestamp
	 * @param array<LessonBlock> $blocks Optional blocks collection
	 * @return self
	 * @throws ValidationException
	 */
	public static function create(
		int $module_id,
		string $title,
		?string $description = null,
		int $sort_order = 0,
		string $status = ContentStatus::DRAFT,
		?int $id = null,
		?DateTime $created_at = null,
		?DateTime $updated_at = null,
		?DateTime $deleted_at = null,
		array $blocks = []
	): self
	{
		$trimmed_title = trim($title);
		if ($trimmed_title === '') {
			throw new ValidationException("Lesson title cannot be empty");
		}

		if (!ContentStatus::is_valid($status)) {
			throw new ValidationException("Invalid lesson status: {$status}");
		}

		if ($sort_order < 0) {
			$sort_order = 0;
		}

		$lesson = new self();
		$lesson->id = $id;
		$lesson->module_id = $module_id;
		$lesson->title = $trimmed_title;
		$lesson->description = $description !== null && trim($description) !== '' ? trim($description) : null;
		$lesson->sort_order = $sort_order;
		$lesson->status = $status;
		$lesson->created_at = $created_at ?? new DateTime();
		$lesson->updated_at = $updated_at ?? ($id === null ? new DateTime() : null);
		$lesson->deleted_at = $deleted_at;
		$lesson->blocks = $blocks;

		return $lesson;
	}

	/**
	 * Update lesson attributes.
	 *
	 * @param string $title
	 * @param string|null $description
	 * @param string|null $status
	 * @return void
	 * @throws ValidationException
	 */
	public function update(
		string $title,
		?string $description = null,
		?string $status = null
	): void
	{
		$trimmed_title = trim($title);
		if ($trimmed_title === '') {
			throw new ValidationException("Lesson title cannot be empty");
		}

		$this->title = $trimmed_title;
		$this->description = $description !== null && trim($description) !== '' ? trim($description) : null;

		if ($status !== null) {
			if (!ContentStatus::is_valid($status)) {
				throw new ValidationException("Invalid lesson status: {$status}");
			}
			$this->status = $status;
		}

		$this->updated_at = new DateTime();
	}

	/**
	 * Publish the lesson.
	 *
	 * @return void
	 */
	public function publish(): void
	{
		$this->status = ContentStatus::PUBLISHED;
		$this->updated_at = new DateTime();
	}

	/**
	 * Unpublish the lesson (set to draft).
	 *
	 * @return void
	 */
	public function unpublish(): void
	{
		$this->status = ContentStatus::DRAFT;
		$this->updated_at = new DateTime();
	}

	/**
	 * Change lesson sort order.
	 *
	 * @param int $new_order
	 * @return void
	 */
	public function change_sort_order(int $new_order): void
	{
		$this->sort_order = max(0, $new_order);
		$this->updated_at = new DateTime();
	}

	/**
	 * Mark lesson as deleted.
	 *
	 * @return void
	 */
	public function delete(): void
	{
		$this->deleted_at = new DateTime();
		$this->updated_at = new DateTime();
	}

	/**
	 * Set content blocks.
	 *
	 * @param array<LessonBlock> $blocks
	 * @return void
	 */
	public function set_blocks(array $blocks): void
	{
		$this->blocks = $blocks;
	}

	public function get_id(): ?int
	{
		return $this->id;
	}

	public function get_module_id(): int
	{
		return $this->module_id;
	}

	public function get_title(): string
	{
		return $this->title;
	}

	public function get_description(): ?string
	{
		return $this->description;
	}

	public function get_sort_order(): int
	{
		return $this->sort_order;
	}

	public function get_status(): string
	{
		return $this->status;
	}

	public function is_published(): bool
	{
		return $this->status === ContentStatus::PUBLISHED;
	}

	public function is_draft(): bool
	{
		return $this->status === ContentStatus::DRAFT;
	}

	public function is_deleted(): bool
	{
		return $this->deleted_at !== null;
	}

	public function get_created_at(): ?DateTime
	{
		return $this->created_at;
	}

	public function get_updated_at(): ?DateTime
	{
		return $this->updated_at;
	}

	public function get_deleted_at(): ?DateTime
	{
		return $this->deleted_at;
	}

	/**
	 * @return array<LessonBlock>
	 */
	public function get_blocks(): array
	{
		return $this->blocks;
	}

	/**
	 * Calculate total duration in seconds dynamically from content blocks.
	 *
	 * @return int
	 */
	public function get_total_duration_in_seconds(): int
	{
		$total = 0;
		foreach ($this->blocks as $block) {
			if ($block instanceof \app\domain\course\blocks\VideoBlock) {
				$total += $block->get_duration_in_seconds();
			} elseif ($block instanceof \app\domain\course\blocks\TextBlock || $block instanceof \app\domain\course\blocks\AttachmentBlock) {
				$total += $block->get_min_reading_time_in_seconds();
			}
		}
		return $total;
	}
}
