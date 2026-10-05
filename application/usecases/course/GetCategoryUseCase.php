<?php

namespace app\usecases\course;

use app\domain\course\Category;
use app\domain\course\repositories\CategoryRepositoryInterface;
use app\domain\exceptions\CategoryNotFoundException;

/**
 * Use case for getting a category by ID.
 */
class GetCategoryUseCase
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
	 * Execute category retrieval.
	 *
	 * @param int $id Category ID
	 * @return Category
	 * @throws CategoryNotFoundException When category not found
	 */
	public function execute(int $id): Category
	{
		$category = $this->category_repository->find_by_id($id);
		if ($category === null) {
			throw new CategoryNotFoundException("Categoria com ID {$id} não foi encontrada.");
		}

		return $category;
	}
}
