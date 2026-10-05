<?php

namespace app\usecases\course;

use app\domain\course\Category;
use app\domain\course\repositories\CategoryRepositoryInterface;

/**
 * Use case for listing course categories.
 */
class ListCategoriesUseCase
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
	 * Execute category list retrieval.
	 *
	 * @param bool $only_active If true, returns only active categories
	 * @return array<Category>
	 */
	public function execute(bool $only_active = false): array
	{
		return $only_active
			? $this->category_repository->find_active()
			: $this->category_repository->find_all();
	}
}
