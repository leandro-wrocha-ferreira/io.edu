<?php

namespace app\usecases\admin;

use app\factories\Model_factory;

/**
 * Use case for listing users with server-side pagination and search.
 */
class ListPaginatedUsersUseCase
{
    /** @var \app\domain\identity\UserRepositoryInterface|\User_model */
    private $user_repository;

    /**
     * Constructor.
     *
     * @param mixed $repository Repository for testing (optional)
     */
    public function __construct($repository = null)
    {
        if ($repository !== null) {
            $this->user_repository = $repository;
        } else {
            $this->user_repository = Model_factory::make('user_model');
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
        $result = $this->user_repository->find_paginated($start, $length, $search, $order_col, $order_dir);
        $total = $this->user_repository->count_all();

        return [
            'data' => $result['data'],
            'recordsFiltered' => $result['recordsFiltered'],
            'recordsTotal' => $total,
        ];
    }
}
