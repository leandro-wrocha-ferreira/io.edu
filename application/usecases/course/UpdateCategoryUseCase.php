<?php

namespace app\usecases\course;

use app\domain\course\Category;
use app\domain\course\constants\CategoryStatus;
use app\domain\course\repositories\CategoryRepositoryInterface;
use app\domain\course\value_objects\CourseSlug;
use app\domain\exceptions\CategoryNotFoundException;
use app\domain\exceptions\DuplicateSlugException;

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
	 * @throws CategoryNotFoundException When category not found
	 * @throws DuplicateSlugException When slug is already taken by another category
	 */
	public function execute(int $id, string $name, ?string $slug = null, string $status = CategoryStatus::ACTIVE): Category
	{
		$category = $this->category_repository->find_by_id($id);
		if ($category === null) {
			throw new CategoryNotFoundException("Categoria com ID {$id} não foi encontrada.");
		}

		$final_slug = !empty($slug) ? trim(strtolower($slug)) : CourseSlug::from_title($name)->get_value();

		$existing = $this->category_repository->find_by_slug($final_slug);
		if ($existing !== null && $existing->get_id() !== $id) {
			throw new DuplicateSlugException("Já existe outra categoria com o slug '{$final_slug}'.");
		}

		$category->set_name($name);
		$category->set_slug($final_slug);
		$category->set_status($status);

		return $this->category_repository->save($category);
	}
}
