<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\education\Course;
use app\domain\education\CourseRepositoryInterface;

/**
 * Course model implementing CourseRepositoryInterface.
 */
class Course_model extends MY_Model implements CourseRepositoryInterface
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'courses';

	/**
	 * Entity class for automatic hydration.
	 *
	 * @var string|null
	 */
	protected ?string $entity_class = Course::class;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Find a course by ID.
	 *
	 * @param int|string $id Course ID
	 * @return Course|null
	 */
	public function find_by_id($id): ?Course
	{
		return parent::find_by_id($id);
	}

	/**
	 * Save a Course entity to database (insert or update).
	 *
	 * @param Course $course
	 * @return int
	 */
	public function save(Course $course): int
	{
		$data = [
			'title'       => $course->get_title(),
			'description' => $course->get_description(),
			'category_id' => $course->get_category_id(),
			'base_price'  => $course->get_base_price(),
		];

		if ($course->get_id() !== null) {
			$this->update($data, ['id' => $course->get_id()]);
			return $course->get_id();
		}

		$id = (int) $this->insert($data);
		$course->set_id($id);
		return $id;
	}

}
