<?php

namespace app\usecases\category;

use app\domain\course\Category;
use app\domain\course\constants\CategoryStatus;
use app\domain\course\repositories\CategoryRepositoryInterface;
use app\domain\course\value_objects\CourseSlug;
use app\domain\exceptions\ConflictException;

/**
 * Use case for creating a course category.
 */
class CreateCategoryUseCase
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
	 * Execute category creation.
	 *
	 * @param string $name Category name
	 * @param string|null $slug Category slug (auto-generated if null)
	 * @param string $status Status ('active' or 'inactive')
	 * @return Category
	 * @throws ConflictException When slug is already taken
	 */
	public function execute(string $name, ?string $slug = null, string $status = CategoryStatus::ACTIVE): Category
	{
		$final_slug = !empty($slug) ? CourseSlug::slugify($slug) : CourseSlug::slugify($name);

		$existing = $this->category_repository->find_by_slug($final_slug);
		if ($existing !== null) {
			throw new ConflictException("Category slug already exists");
		}

		$category = Category::create($name, $final_slug, $status);
		return $this->category_repository->create($category);
	}
}
