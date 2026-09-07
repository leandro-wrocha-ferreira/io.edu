<?php
namespace app\usecases\education;

use app\domain\education\Course;
use app\domain\education\CourseRepositoryInterface;
use app\domain\exceptions\ValidationException;
use app\factories\Model_factory;

/**
 * Use case to create a new course in the catalog.
 */
class CreateCourseUseCase
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
	 * Execute course creation.
	 *
	 * @param string $title
	 * @param float $base_price
	 * @param string|null $description
	 * @param int|null $category_id
	 * @return Course
	 * @throws ValidationException
	 */
	public function execute(string $title, float $base_price = 0.00, ?string $description = null, ?int $category_id = null): Course
	{
		$trimmed_title = trim($title);
		if (empty($trimmed_title)) {
			throw new ValidationException("Course title is required.");
		}

		if ($base_price < 0) {
			throw new ValidationException("Course base price cannot be negative.");
		}

		$course = Course::create($trimmed_title, $base_price, $category_id);
		if ($description !== null) {
			$course->set_description($description);
		}

		$id = $this->course_repository->save($course);
		$course->set_id($id);

		return $course;
	}
}
