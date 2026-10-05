<?php

namespace app\usecases\course;

use app\domain\course\Category;
use app\domain\course\constants\CategoryStatus;
use app\domain\course\repositories\CategoryRepositoryInterface;
use app\domain\course\value_objects\CourseSlug;
use app\domain\exceptions\DuplicateSlugException;

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
	 * @throws DuplicateSlugException When slug is already taken
	 */
	public function execute(string $name, ?string $slug = null, string $status = CategoryStatus::ACTIVE): Category
	{
		$final_slug = !empty($slug) ? trim(strtolower($slug)) : CourseSlug::from_title($name)->get_value();

		$existing = $this->category_repository->find_by_slug($final_slug);
		if ($existing !== null) {
			throw new DuplicateSlugException("Já existe uma categoria com o slug '{$final_slug}'.");
		}

		$category = Category::create($name, $final_slug, $status);
		return $this->category_repository->create($category);
	}
}
