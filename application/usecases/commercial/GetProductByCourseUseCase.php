<?php

namespace app\usecases\commercial;

use app\domain\commercial\Product;
use app\domain\commercial\repositories\ProductRepositoryInterface;

/**
 * Use case for retrieving the product associated with a course.
 */
class GetProductByCourseUseCase
{
	/** @var ProductRepositoryInterface */
	private ProductRepositoryInterface $product_repository;

	/**
	 * Constructor.
	 *
	 * @param ProductRepositoryInterface $product_repository
	 */
	public function __construct(ProductRepositoryInterface $product_repository)
	{
		$this->product_repository = $product_repository;
	}

	/**
	 * Execute use case.
	 *
	 * @param int $course_id Associated course ID
	 * @return Product|null
	 */
	public function execute(int $course_id): ?Product
	{
		return $this->product_repository->find_by_course_id($course_id);
	}
}
