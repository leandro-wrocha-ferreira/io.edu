<?php

namespace app\domain\course;

use app\domain\course\constants\ContentStatus;
use app\domain\exceptions\ValidationException;
use DateTime;

/**
 * Domain entity representing a pedagogical Course Module.
 */
final class CourseModule
{
	/**
	 * Unique identifier.
	 *
	 * @var int|null
	 */
	private ?int $id = null;

	/**
	 * Parent course ID.
	 *
	 * @var int
	 */
	private int $course_id;

	/**
	 * Module title.
	 *
	 * @var string
	 */
	private string $title;

	/**
	 * Module description or synopsis.
	 *
	 * @var string|null
	 */
	private ?string $description = null;

	/**
	 * Sequential sort order within the course.
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
	 * Lessons in this module (optional collection projection).
	 *
	 * @var array<CourseLesson>
	 */
	private array $lessons = [];

	/**
	 * Private constructor.
	 */
	private function __construct()
	{
	}

	/**
	 * Unified factory method to create a CourseModule domain entity.
	 *
	 * @param int $course_id Parent course ID
	 * @param string $title Module title
	 * @param string|null $description Optional description
	 * @param int $sort_order Sequential sort order
	 * @param string $status Status ('draft', 'published')
	 * @param int|null $id Module ID
	 * @param DateTime|null $created_at Creation timestamp
	 * @param DateTime|null $updated_at Update timestamp
	 * @param DateTime|null $deleted_at Deletion timestamp
	 * @param array<CourseLesson> $lessons Optional lessons collection
	 * @return self
	 * @throws ValidationException
	 */
	public static function create(
		int $course_id,
		string $title,
		?string $description = null,
		int $sort_order = 0,
		string $status = ContentStatus::DRAFT,
		?int $id = null,
		?DateTime $created_at = null,
		?DateTime $updated_at = null,
		?DateTime $deleted_at = null,
		array $lessons = []
	): self
	{
		$trimmed_title = trim($title);
		if ($trimmed_title === '') {
			throw new ValidationException("Module title cannot be empty");
		}

		if (!ContentStatus::is_valid($status)) {
			throw new ValidationException("Invalid module status: {$status}");
		}

		if ($sort_order < 0) {
			$sort_order = 0;
		}

		$module = new self();
		$module->id = $id;
		$module->course_id = $course_id;
		$module->title = $trimmed_title;
		$module->description = $description !== null && trim($description) !== '' ? trim($description) : null;
		$module->sort_order = $sort_order;
		$module->status = $status;
		$module->created_at = $created_at ?? new DateTime();
		$module->updated_at = $updated_at ?? ($id === null ? new DateTime() : null);
		$module->deleted_at = $deleted_at;
		$module->lessons = $lessons;

		return $module;
	}

	/**
	 * Update module identification details.
	 *
	 * @param string $title
	 * @param string|null $description
	 * @param string|null $status
	 * @return void
	 * @throws ValidationException
	 */
	public function update(string $title, ?string $description = null, ?string $status = null): void
	{
		$trimmed_title = trim($title);
		if ($trimmed_title === '') {
			throw new ValidationException("Module title cannot be empty");
		}

		$this->title = $trimmed_title;
		$this->description = $description !== null && trim($description) !== '' ? trim($description) : null;

		if ($status !== null) {
			if (!ContentStatus::is_valid($status)) {
				throw new ValidationException("Invalid module status: {$status}");
			}
			$this->status = $status;
		}

		$this->updated_at = new DateTime();
	}

	/**
	 * Publish the module.
	 *
	 * @return void
	 */
	public function publish(): void
	{
		$this->status = ContentStatus::PUBLISHED;
		$this->updated_at = new DateTime();
	}

	/**
	 * Unpublish the module (set to draft).
	 *
	 * @return void
	 */
	public function unpublish(): void
	{
		$this->status = ContentStatus::DRAFT;
		$this->updated_at = new DateTime();
	}

	/**
	 * Change module sort order.
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
	 * Mark module as deleted.
	 *
	 * @return void
	 */
	public function delete(): void
	{
		$this->deleted_at = new DateTime();
		$this->updated_at = new DateTime();
	}

	/**
	 * Set lessons collection for projection.
	 *
	 * @param array<CourseLesson> $lessons
	 * @return void
	 */
	public function set_lessons(array $lessons): void
	{
		$this->lessons = $lessons;
	}

	/**
	 * Calculate total duration in seconds from lessons.
	 *
	 * @return int
	 */
	public function get_total_duration_in_seconds(): int
	{
		$total = 0;
		foreach ($this->lessons as $lesson) {
			$total += $lesson->get_total_duration_in_seconds();
		}
		return $total;
	}

	public function get_id(): ?int
	{
		return $this->id;
	}

	public function get_course_id(): int
	{
		return $this->course_id;
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
	 * @return array<CourseLesson>
	 */
	public function get_lessons(): array
	{
		return $this->lessons;
	}
}
