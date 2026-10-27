<?php

namespace app\usecases\finance;

use app\domain\exceptions\ConflictException;
use app\domain\exceptions\NotFoundException;
use app\domain\finance\repositories\CostCenterRepositoryInterface;

/**
 * Use case for soft deleting a cost center.
 */
class DeleteCostCenterUseCase
{
	/** @var CostCenterRepositoryInterface */
	private CostCenterRepositoryInterface $cost_center_repository;

	/**
	 * Constructor.
	 *
	 * @param CostCenterRepositoryInterface $cost_center_repository
	 */
	public function __construct(CostCenterRepositoryInterface $cost_center_repository)
	{
		$this->cost_center_repository = $cost_center_repository;
	}

	/**
	 * Execute soft delete.
	 *
	 * @param int $id Cost center ID
	 * @return bool
	 * @throws NotFoundException When cost center not found
	 * @throws ConflictException When cost center has linked products
	 */
	public function execute(int $id): bool
	{
		$cost_center = $this->cost_center_repository->find_by_id($id);
		if ($cost_center === null) {
			throw new NotFoundException("Cost center not found");
		}

		if ($this->cost_center_repository->has_linked_products($id)) {
			throw new ConflictException("Cannot delete cost center with linked products");
		}

		return $this->cost_center_repository->delete($id);
	}
}
