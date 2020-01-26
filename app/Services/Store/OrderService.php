<?php

namespace App\Services\Store;

use App\Models\Order;
use App\Notifications\OrderStatusChanged;
use Illuminate\Http\Response;

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

    /**
     * This function changes order Status
     *
     * @param int    $orderId  The id of order
     * @param string $status   New status name
     *
     * @return \App\Models\Order
     */
    public function changeStatus(
        $orderId,
        $status
    ) {
        $order = $this->order->with('user')->findOrFail($orderId);

        if ($order->status == $status) {
            abort(response()->json([
                'status' => 'Following status already exists.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        $order->status = $status;
        $order->save();

        $order->user->notify(new OrderStatusChanged(
            $order->user,
            $order
        ));

        return $order;
    }
}
