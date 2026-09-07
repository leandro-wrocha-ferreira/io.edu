<?php
namespace app\usecases\education;

use app\domain\education\Course;
use app\domain\education\CourseRepositoryInterface;
use app\factories\Model_factory;

/**
 * Use case to list courses in the catalog.
 */
class ListCoursesUseCase
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
	 * Execute listing.
	 *
	 * @return Course[]
	 */
	public function execute(): array
	{
		return $this->course_repository->find_all();
	}
}
