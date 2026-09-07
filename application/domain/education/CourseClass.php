<?php

namespace app\domain\education;

use DateTime;

/**
 * CourseClass Entity (Turma).
 */
class CourseClass
{
	private ?int $id = null;
	private int $course_id;
	private string $name;
	private string $modality;
	private ?float $price = null;
	private ?DateTime $enrollment_start = null;
	private ?DateTime $enrollment_end = null;
	private ?int $max_students = null;
	private int $total_duration_seconds = 0;

	/**
	 * Create a new CourseClass instance.
	 *
	 * @param int $course_id Associated course ID
	 * @param string $name Class name
	 * @param string $modality Modality (online/presencial)
	 * @return self
	 */
	public static function create(int $course_id, string $name, string $modality = 'online'): self
	{
		$class = new self();
		$class->course_id = $course_id;
		$class->name = $name;
		$class->modality = $modality;
		return $class;
	}

	/**
	 * Hydrate a CourseClass entity from database row.
	 *
	 * @param array $row Database row
	 * @return self
	 */
	public static function from_database(array $row): self
	{
		$class = new self();
		$class->id = (int) $row['id'];
		$class->course_id = (int) $row['course_id'];
		$class->name = $row['name'];
		$class->modality = $row['modality'];
		$class->price = isset($row['price']) ? (float) $row['price'] : null;
		$class->enrollment_start = isset($row['enrollment_start']) ? new DateTime($row['enrollment_start']) : null;
		$class->enrollment_end = isset($row['enrollment_end']) ? new DateTime($row['enrollment_end']) : null;
		$class->max_students = isset($row['max_students']) ? (int) $row['max_students'] : null;
		$class->total_duration_seconds = (int) $row['total_duration_seconds'];
		return $class;
	}

	/**
	 * Get class ID.
	 *
	 * @return int|null
	 */
	public function get_id(): ?int
	{
		return $this->id;
	}

	/**
	 * Set class ID.
	 *
	 * @param int $id
	 * @return self
	 */
	public function set_id(int $id): self
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * Get associated course ID.
	 *
	 * @return int
	 */
	public function get_course_id(): int
	{
		return $this->course_id;
	}

	/**
	 * Set associated course ID.
	 *
	 * @param int $course_id
	 * @return self
	 */
	public function set_course_id(int $course_id): self
	{
		$this->course_id = $course_id;
		return $this;
	}

	/**
	 * Get class name.
	 *
	 * @return string
	 */
	public function get_name(): string
	{
		return $this->name;
	}

	/**
	 * Set class name.
	 *
	 * @param string $name
	 * @return self
	 */
	public function set_name(string $name): self
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * Get modality.
	 *
	 * @return string
	 */
	public function get_modality(): string
	{
		return $this->modality;
	}

	/**
	 * Set modality.
	 *
	 * @param string $modality
	 * @return self
	 */
	public function set_modality(string $modality): self
	{
		$this->modality = $modality;
		return $this;
	}

	/**
	 * Get class price.
	 *
	 * @return float|null
	 */
	public function get_price(): ?float
	{
		return $this->price;
	}

	/**
	 * Set class price.
	 *
	 * @param float|null $price
	 * @return self
	 */
	public function set_price(?float $price): self
	{
		$this->price = $price;
		return $this;
	}

	/**
	 * Get enrollment start date.
	 *
	 * @return DateTime|null
	 */
	public function get_enrollment_start(): ?DateTime
	{
		return $this->enrollment_start;
	}

	/**
	 * Set enrollment start date.
	 *
	 * @param DateTime|null $start
	 * @return self
	 */
	public function set_enrollment_start(?DateTime $start): self
	{
		$this->enrollment_start = $start;
		return $this;
	}

	/**
	 * Get enrollment end date.
	 *
	 * @return DateTime|null
	 */
	public function get_enrollment_end(): ?DateTime
	{
		return $this->enrollment_end;
	}

	/**
	 * Set enrollment end date.
	 *
	 * @param DateTime|null $end
	 * @return self
	 */
	public function set_enrollment_end(?DateTime $end): self
	{
		$this->enrollment_end = $end;
		return $this;
	}

	/**
	 * Get max students count.
	 *
	 * @return int|null
	 */
	public function get_max_students(): ?int
	{
		return $this->max_students;
	}

	/**
	 * Set max students count.
	 *
	 * @param int|null $max
	 * @return self
	 */
	public function set_max_students(?int $max): self
	{
		$this->max_students = $max;
		return $this;
	}

	/**
	 * Get total duration in seconds.
	 *
	 * @return int
	 */
	public function get_total_duration_seconds(): int
	{
		return $this->total_duration_seconds;
	}

	/**
	 * Set total duration in seconds.
	 *
	 * @param int $seconds
	 * @return self
	 */
	public function set_total_duration_seconds(int $seconds): self
	{
		$this->total_duration_seconds = $seconds;
		return $this;
	}

	/**
	 * Calculate effective price, falling back to course base price if null.
	 *
	 * @param Course $course
	 * @return float
	 */
	public function get_effective_price(Course $course): float
	{
		return $this->price !== null ? $this->price : $course->get_base_price();
	}

	/**
	 * Check if enrollment is currently open based on dates.
	 *
	 * @return bool
	 */
	public function is_enrollment_open(): bool
	{
		$now = new DateTime();

		if ($this->enrollment_start !== null && $now < $this->enrollment_start) {
			return false;
		}

		if ($this->enrollment_end !== null && $now > $this->enrollment_end) {
			return false;
		}

		return true;
	}
}
