<?php

namespace App\Services\Store;

use App\Models\Order;

class OrderService
{
    /**
     * The Order model instance
     *
     * @var App\Models\Order
     */
    protected $order = null;

    public function __construct (Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get all orders with pagination
     *
     * @param int $page  The page number
     * @param int $items The number of items per page
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index (
        $page = 1,
        $items = 15
    ) {
        return $this->order->with('user')->with('products')
            ->paginate(
                $items,
                ['*'],
                'page',
                $page
            );
    }
}
