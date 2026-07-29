<?php

namespace app\usecases\admin;

use app\factories\Model_factory;

/**
 * Use case for listing roles with server-side pagination and search.
 */
class ListPaginatedRolesUseCase
{
    /** @var \app\domain\admin\RoleRepositoryInterface|\Role_model */
    private $role_repository;

    /**
     * Constructor.
     *
     * @param mixed $repository Repository for testing (optional)
     */
    public function __construct($repository = null)
    {
        if ($repository !== null) {
            $this->role_repository = $repository;
        } else {
            $this->role_repository = Model_factory::make('role_model');
        }
    }

    /**
     * Execute the use case.
     *
     * @param int $start Offset
     * @param int $length Page size
     * @param string $search Global search term
     * @param string $order_col Column name to order by
     * @param string $order_dir ASC or DESC
     * @return array ['data' => array, 'recordsFiltered' => int, 'recordsTotal' => int]
     */
    public function execute(int $start, int $length, string $search, string $order_col, string $order_dir): array
    {
        $result = $this->role_repository->find_paginated($start, $length, $search, $order_col, $order_dir);
        $total = $this->role_repository->count_all();

        return [
            'data' => $result['data'],
            'recordsFiltered' => $result['recordsFiltered'],
            'recordsTotal' => $total,
        ];
    }
}
