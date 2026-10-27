<?php

namespace app\usecases\finance;

use app\domain\finance\CostCenter;
use app\domain\finance\repositories\CostCenterRepositoryInterface;

/**
 * Use case for creating a new cost center.
 */
class CreateCostCenterUseCase
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
	 * Execute cost center creation.
	 *
	 * @param string $name Cost center name
	 * @param string|null $description Optional description
	 * @param bool $is_active Active status
	 * @return CostCenter
	 */
	public function execute(string $name, ?string $description = null, bool $is_active = true): CostCenter
	{
		$cost_center = CostCenter::create($name, $description, $is_active);

		return $this->cost_center_repository->save($cost_center);
	}
}
