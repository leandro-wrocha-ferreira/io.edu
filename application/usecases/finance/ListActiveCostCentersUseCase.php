<?php

namespace app\usecases\finance;

use app\domain\finance\CostCenter;
use app\domain\finance\repositories\CostCenterRepositoryInterface;

/**
 * Use case for listing active cost centers.
 */
class ListActiveCostCentersUseCase
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
	 * @return array<CostCenter>
	 */
	public function execute(): array
	{
		return $this->cost_center_repository->find_all_active();
	}
}
