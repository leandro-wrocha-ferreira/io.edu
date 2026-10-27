<?php

namespace app\usecases\finance;

use app\domain\exceptions\NotFoundException;
use app\domain\finance\CostCenter;
use app\domain\finance\repositories\CostCenterRepositoryInterface;

/**
 * Use case for updating an existing cost center.
 */
class UpdateCostCenterUseCase
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
	 * Execute cost center update.
	 *
	 * @param int $id Cost center ID
	 * @param string $name Cost center name
	 * @param string|null $description Optional description
	 * @param bool $is_active Active status
	 * @return CostCenter
	 * @throws NotFoundException When cost center not found
	 */
	public function execute(
		int $id,
		string $name,
		?string $description = null,
		bool $is_active = true
	): CostCenter
	{
		$cost_center = $this->cost_center_repository->find_by_id($id);
		if ($cost_center === null) {
			throw new NotFoundException("Cost center not found");
		}

		$cost_center->update($name, $description, $is_active);

		return $this->cost_center_repository->save($cost_center);
	}
}
