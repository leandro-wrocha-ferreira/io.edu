<?php

namespace app\domain\course;

use app\domain\course\value_objects\CourseAccessPeriod;
use app\domain\course\value_objects\CourseSlug;
use app\domain\course\value_objects\CourseStatus;
use app\domain\course\value_objects\Workload;
use DateTime;
use InvalidArgumentException;

/**
 * Domain entity representing a LMS Course product.
 */
final class Course
{
	/**
	 * Unique identifier.
	 *
	 * @var int|null
	 */
	private ?int $id = null;

	/**
	 * Associated category ID.
	 *
	 * @var int
	 */
	private int $category_id;

	/**
	 * Associated category name (optional projection).
	 *
	 * @var string|null
	 */
	private ?string $category_name = null;

	/**
	 * Course title.
	 *
	 * @var string
	 */
	private string $title;

	/**
	 * Course URL slug.
	 *
	 * @var CourseSlug
	 */
	private CourseSlug $slug;

	/**
	 * Short summary description for showcase cards.
	 *
	 * @var string|null
	 */
	private ?string $short_description = null;

	/**
	 * Complete description and syllabus details.
	 *
	 * @var string|null
	 */
	private ?string $description = null;

	/**
	 * Cover image path or URL.
	 *
	 * @var string|null
	 */
	private ?string $image = null;

	/**
	 * Publication status ('draft', 'active', 'archived').
	 *
	 * @var CourseStatus
	 */
	private CourseStatus $status;

	/**
	 * Estimated workload in hours.
	 *
	 * @var Workload|null
	 */
	private ?Workload $workload = null;

	/**
	 * Total recorded media duration in seconds.
	 *
	 * @var int
	 */
	private int $duration_in_seconds = 0;

	/**
	 * Learning objectives.
	 *
	 * @var string|null
	 */
	private ?string $objectives = null;

	/**
	 * Target audience profile.
	 *
	 * @var string|null
	 */
	private ?string $target_audience = null;

	/**
	 * Recommended prerequisites.
	 *
	 * @var string|null
	 */
	private ?string $requirements = null;

	/**
	 * Access period configuration (lifetime vs limited time).
	 *
	 * @var CourseAccessPeriod
	 */
	private CourseAccessPeriod $access_period;

	/**
	 * Whether a certificate is issued upon course completion.
	 *
	 * @var bool
	 */
	private bool $certificate_enabled = true;

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
	 * Deletion timestamp (soft delete).
	 *
	 * @var DateTime|null
	 */
	private ?DateTime $deleted_at = null;

	/**
	 * Create a course domain entity.
	 *
	 * @param int $category_id Category foreign key ID
	 * @param string $title Course title
	 * @param CourseSlug|string $slug Course slug Value Object or string
	 * @param CourseStatus|string $status Status ('draft', 'active', 'archived')
	 * @param CourseAccessPeriod|null $access_period Access period VO
	 * @param Workload|int|null $workload Workload VO or hours
	 * @param string|null $short_description Short summary
	 * @param string|null $description Detailed description
	 * @param string|null $image Cover image URL/path
	 * @param int $duration_in_seconds Media duration in seconds
	 * @param string|null $objectives Learning objectives
	 * @param string|null $target_audience Target audience
	 * @param string|null $requirements Prerequisites
	 * @param bool $certificate_enabled Certificate toggle
	 * @param int|null $id Course ID
	 * @param DateTime|null $created_at Creation timestamp
	 * @param DateTime|null $updated_at Update timestamp
	 * @param DateTime|null $deleted_at Deletion timestamp
	 * @param string|null $category_name Optional category name
	 * @return self
	 * @throws InvalidArgumentException When validation fails
	 */
	public static function create(
		int $category_id,
		string $title,
		CourseSlug|string $slug,
		CourseStatus|string $status = CourseStatus::DRAFT,
		?CourseAccessPeriod $access_period = null,
		Workload|int|null $workload = null,
		?string $short_description = null,
		?string $description = null,
		?string $image = null,
		int $duration_in_seconds = 0,
		?string $objectives = null,
		?string $target_audience = null,
		?string $requirements = null,
		bool $certificate_enabled = true,
		?int $id = null,
		?DateTime $created_at = null,
		?DateTime $updated_at = null,
		?DateTime $deleted_at = null,
		?string $category_name = null
	): self
	{
		if ($category_id <= 0) {
			throw new InvalidArgumentException("Category ID must be greater than 0");
		}

		$trimmed_title = trim($title);
		if ($trimmed_title === '') {
			throw new InvalidArgumentException("Course title cannot be empty");
		}

		$course_slug = $slug instanceof CourseSlug ? $slug : new CourseSlug($slug);
		$course_status = $status instanceof CourseStatus ? $status : new CourseStatus($status);
		$course_access_period = $access_period ?? CourseAccessPeriod::lifetime();

		$course_workload = null;
		if ($workload instanceof Workload) {
			$course_workload = $workload;
		} elseif ($workload !== null) {
			$course_workload = new Workload((int) $workload);
		}

		$course = new self();
		$course->id = $id;
		$course->category_id = $category_id;
		$course->category_name = $category_name;
		$course->title = $trimmed_title;
		$course->slug = $course_slug;
		$course->status = $course_status;
		$course->access_period = $course_access_period;
		$course->workload = $course_workload;
		$course->short_description = $short_description !== null ? trim($short_description) : null;
		$course->description = $description !== null ? trim($description) : null;
		$course->image = $image !== null ? trim($image) : null;
		$course->duration_in_seconds = max(0, $duration_in_seconds);
		$course->objectives = $objectives !== null ? trim($objectives) : null;
		$course->target_audience = $target_audience !== null ? trim($target_audience) : null;
		$course->requirements = $requirements !== null ? trim($requirements) : null;
		$course->certificate_enabled = $certificate_enabled;
		$course->created_at = $created_at ?? new DateTime();
		$course->updated_at = $updated_at ?? ($id === null ? new DateTime() : null);
		$course->deleted_at = $deleted_at;

		return $course;
	}

	/**
	 * Update general course details.
	 *
	 * @param int $category_id
	 * @param string $title
	 * @param CourseSlug $slug
	 * @param CourseAccessPeriod $access_period
	 * @param Workload|null $workload
	 * @param string|null $short_description
	 * @param string|null $description
	 * @param string|null $image
	 * @param string|null $objectives
	 * @param string|null $target_audience
	 * @param string|null $requirements
	 * @param bool $certificate_enabled
	 * @return void
	 */
	public function update_details(
		int $category_id,
		string $title,
		CourseSlug $slug,
		CourseAccessPeriod $access_period,
		?Workload $workload = null,
		?string $short_description = null,
		?string $description = null,
		?string $image = null,
		?string $objectives = null,
		?string $target_audience = null,
		?string $requirements = null,
		bool $certificate_enabled = true
	): void
	{
		if ($category_id <= 0) {
			throw new InvalidArgumentException("Category ID must be greater than 0");
		}

		$trimmed_title = trim($title);
		if ($trimmed_title === '') {
			throw new InvalidArgumentException("Course title cannot be empty");
		}

		$this->category_id = $category_id;
		$this->title = $trimmed_title;
		$this->slug = $slug;
		$this->access_period = $access_period;
		$this->workload = $workload;
		$this->short_description = $short_description !== null ? trim($short_description) : null;
		$this->description = $description !== null ? trim($description) : null;
		$this->image = $image !== null ? trim($image) : null;
		$this->objectives = $objectives !== null ? trim($objectives) : null;
		$this->target_audience = $target_audience !== null ? trim($target_audience) : null;
		$this->requirements = $requirements !== null ? trim($requirements) : null;
		$this->certificate_enabled = $certificate_enabled;
	}

	/**
	 * Publish the course.
	 *
	 * @return void
	 */
	public function publish(): void
	{
		$this->status = CourseStatus::active();
	}

	/**
	 * Archive the course.
	 *
	 * @return void
	 */
	public function archive(): void
	{
		$this->status = CourseStatus::archived();
	}

	/**
	 * Set status back to draft.
	 *
	 * @return void
	 */
	public function draft(): void
	{
		$this->status = CourseStatus::draft();
	}

	/**
	 * Mark course as soft-deleted.
	 *
	 * @return void
	 */
	public function delete(): void
	{
		$this->deleted_at = new DateTime();
	}

	/**
	 * Check if course is active.
	 *
	 * @return bool
	 */
	public function is_active(): bool
	{
		return $this->status->is_active() && !$this->is_deleted();
	}

	/**
	 * Check if course is draft.
	 *
	 * @return bool
	 */
	public function is_draft(): bool
	{
		return $this->status->is_draft();
	}

	/**
	 * Check if course is archived.
	 *
	 * @return bool
	 */
	public function is_archived(): bool
	{
		return $this->status->is_archived();
	}

	/**
	 * Check if course is deleted.
	 *
	 * @return bool
	 */
	public function is_deleted(): bool
	{
		return $this->deleted_at !== null;
	}

	/**
	 * Get course ID.
	 *
	 * @return int|null
	 */
	public function get_id(): ?int
	{
		return $this->id;
	}

	/**
	 * Get category ID.
	 *
	 * @return int
	 */
	public function get_category_id(): int
	{
		return $this->category_id;
	}

	/**
	 * Get category name.
	 *
	 * @return string|null
	 */
	public function get_category_name(): ?string
	{
		return $this->category_name;
	}

	/**
	 * Get title.
	 *
	 * @return string
	 */
	public function get_title(): string
	{
		return $this->title;
	}

	/**
	 * Get slug VO.
	 *
	 * @return CourseSlug
	 */
	public function get_slug(): CourseSlug
	{
		return $this->slug;
	}

	/**
	 * Get status VO.
	 *
	 * @return CourseStatus
	 */
	public function get_status(): CourseStatus
	{
		return $this->status;
	}

	/**
	 * Get short description.
	 *
	 * @return string|null
	 */
	public function get_short_description(): ?string
	{
		return $this->short_description;
	}

	/**
	 * Get description.
	 *
	 * @return string|null
	 */
	public function get_description(): ?string
	{
		return $this->description;
	}

	/**
	 * Get image.
	 *
	 * @return string|null
	 */
	public function get_image(): ?string
	{
		return $this->image;
	}

	/**
	 * Get workload VO.
	 *
	 * @return Workload|null
	 */
	public function get_workload(): ?Workload
	{
		return $this->workload;
	}

	/**
	 * Get workload in hours.
	 *
	 * @return int|null
	 */
	public function get_workload_in_hours(): ?int
	{
		return $this->workload !== null ? $this->workload->get_hours() : null;
	}

	/**
	 * Get duration in seconds.
	 *
	 * @return int
	 */
	public function get_duration_in_seconds(): int
	{
		return $this->duration_in_seconds;
	}

	/**
	 * Get objectives.
	 *
	 * @return string|null
	 */
	public function get_objectives(): ?string
	{
		return $this->objectives;
	}

	/**
	 * Get target audience.
	 *
	 * @return string|null
	 */
	public function get_target_audience(): ?string
	{
		return $this->target_audience;
	}

	/**
	 * Get requirements.
	 *
	 * @return string|null
	 */
	public function get_requirements(): ?string
	{
		return $this->requirements;
	}

	/**
	 * Get access period VO.
	 *
	 * @return CourseAccessPeriod
	 */
	public function get_access_period(): CourseAccessPeriod
	{
		return $this->access_period;
	}

	/**
	 * Check whether certificate is enabled.
	 *
	 * @return bool
	 */
	public function is_certificate_enabled(): bool
	{
		return $this->certificate_enabled;
	}

	/**
	 * Get creation timestamp.
	 *
	 * @return DateTime|null
	 */
	public function get_created_at(): ?DateTime
	{
		return $this->created_at;
	}

	/**
	 * Get update timestamp.
	 *
	 * @return DateTime|null
	 */
	public function get_updated_at(): ?DateTime
	{
		return $this->updated_at;
	}

	/**
	 * Get deletion timestamp.
	 *
	 * @return DateTime|null
	 */
	public function get_deleted_at(): ?DateTime
	{
		return $this->deleted_at;
	}
}
