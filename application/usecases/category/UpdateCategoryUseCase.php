<?php

namespace app\usecases\category;

use app\domain\course\Category;
use app\domain\course\constants\CategoryStatus;
use app\domain\course\repositories\CategoryRepositoryInterface;
use app\domain\course\value_objects\CourseSlug;
use app\domain\exceptions\ConflictException;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for updating a course category.
 */
class UpdateCategoryUseCase
{
	/** @var CategoryRepositoryInterface */
	private CategoryRepositoryInterface $category_repository;

	/**
	 * Constructor.
	 *
	 * @param CategoryRepositoryInterface $category_repository
	 */
	public function __construct(CategoryRepositoryInterface $category_repository)
	{
		$this->category_repository = $category_repository;
	}

	/**
	 * Execute category update.
	 *
	 * @param int $id Category ID
	 * @param string $name Category name
	 * @param string|null $slug Category slug (auto-generated if null)
	 * @param string $status Status ('active' or 'inactive')
	 * @return Category
	 * @throws NotFoundException When category not found
	 * @throws ConflictException When slug is already taken by another category
	 */
	public function execute(int $id, string $name, ?string $slug = null, string $status = CategoryStatus::ACTIVE): Category
	{
		$category = $this->category_repository->find_by_id($id);
		if ($category === null) {
			throw new NotFoundException("Category not found");
		}

		$final_slug = !empty($slug) ? CourseSlug::slugify($slug) : CourseSlug::slugify($name);

		$existing = $this->category_repository->find_by_slug($final_slug);
		if ($existing !== null && $existing->get_id() !== $id) {
			throw new ConflictException("Category slug already exists");
		}

		$category->set_name($name);
		$category->set_slug($final_slug);
		$category->set_status($status);

		return $this->category_repository->save($category);
	}
}
