<?php

namespace app\usecases\commercial;

use app\domain\commercial\ProductPriceVersion;
use app\domain\commercial\repositories\ProductPriceVersionRepositoryInterface;

/**
 * Use case for listing immutable price versions for a product.
 */
class ListProductPriceVersionsUseCase
{
	/** @var ProductPriceVersionRepositoryInterface */
	private ProductPriceVersionRepositoryInterface $price_version_repository;

	/**
	 * Constructor.
	 *
	 * @param ProductPriceVersionRepositoryInterface $price_version_repository
	 */
	public function __construct(ProductPriceVersionRepositoryInterface $price_version_repository)
	{
		$this->price_version_repository = $price_version_repository;
	}

	/**
	 * Execute use case.
	 *
	 * @param int $product_id Product ID
	 * @return array<ProductPriceVersion>
	 */
	public function execute(int $product_id): array
	{
		return $this->price_version_repository->list_by_product_id($product_id);
	}
}
