<?php
namespace app\usecases\education;

use app\domain\education\Course;
use app\domain\education\CourseRepositoryInterface;
use app\domain\exceptions\NotFoundException;
use app\domain\exceptions\ValidationException;
use app\factories\Model_factory;

/**
 * Use case to update an existing course.
 */
class UpdateCourseUseCase
{
	/** @var CourseRepositoryInterface */
	private CourseRepositoryInterface $course_repository;

	/**
	 * Constructor.
	 *
	 * @param CourseRepositoryInterface|null $repository
	 */
	public function __construct(?CourseRepositoryInterface $repository = null)
	{
		$this->course_repository = $repository ?? Model_factory::make('course_model');
	}

	/**
	 * Execute course update.
	 *
	 * @param int $course_id
	 * @param string $title
	 * @param float $base_price
	 * @param string|null $description
	 * @param int|null $category_id
	 * @return Course
	 * @throws NotFoundException
	 * @throws ValidationException
	 */
	public function execute(int $course_id, string $title, float $base_price = 0.00, ?string $description = null, ?int $category_id = null): Course
	{
		$course = $this->course_repository->find_by_id($course_id);
		if ($course === null) {
			throw new NotFoundException("Course not found with ID {$course_id}.");
		}

		$trimmed_title = trim($title);
		if (empty($trimmed_title)) {
			throw new ValidationException("Course title cannot be empty.");
		}

		if ($base_price < 0) {
			throw new ValidationException("Course base price cannot be negative.");
		}

		$course->set_title($trimmed_title)
			->set_base_price($base_price)
			->set_description($description)
			->set_category_id($category_id);

		$this->course_repository->save($course);

		return $course;
	}
}
