<?php

namespace app\usecases\category;

use app\domain\course\Category;
use app\domain\course\repositories\CategoryRepositoryInterface;
use app\domain\course\repositories\CourseRepositoryInterface;
use app\domain\exceptions\ConflictException;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for deleting a course category.
 */
class DeleteCategoryUseCase
{
	/** @var CategoryRepositoryInterface */
	private CategoryRepositoryInterface $category_repository;

	/** @var CourseRepositoryInterface */
	private CourseRepositoryInterface $course_repository;

	/**
	 * Constructor.
	 *
	 * @param CategoryRepositoryInterface $category_repository
	 * @param CourseRepositoryInterface $course_repository
	 */
	public function __construct(
		CategoryRepositoryInterface $category_repository,
		CourseRepositoryInterface $course_repository
	)
	{
		$this->category_repository = $category_repository;
		$this->course_repository = $course_repository;
	}

	/**
	 * Execute category deletion.
	 *
	 * @param int $id Category ID
	 * @return Category
	 * @throws NotFoundException When category not found
	 * @throws ConflictException When category has linked courses
	 */
	public function execute(int $id): Category
	{
		$category = $this->category_repository->find_by_id($id);
		if ($category === null) {
			throw new NotFoundException("Category not found");
		}

		$course_count = $this->course_repository->count_by_category_id($id);
		if ($course_count > 0) {
			throw new ConflictException("Cannot delete category with linked courses");
		}

		return $this->category_repository->delete($category);
	}
}
