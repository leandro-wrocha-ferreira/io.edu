<?php

namespace app\usecases\finance;

use app\domain\exceptions\NotFoundException;
use app\domain\finance\CostCenter;
use app\domain\finance\repositories\CostCenterRepositoryInterface;

/**
 * Use case for retrieving a specific cost center by ID.
 */
class GetCostCenterUseCase
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
	 * Execute use case.
	 *
	 * @param int $id Cost center identifier
	 * @return CostCenter
	 * @throws NotFoundException When cost center does not exist
	 */
	public function execute(int $id): CostCenter
	{
		$cost_center = $this->cost_center_repository->find_by_id($id);
		if ($cost_center === null) {
			throw new NotFoundException("Cost center not found");
		}

		return $cost_center;
	}
}
