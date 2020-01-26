<?php

namespace App\Http\Controllers\Store;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Transformers\OrderTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use App\Http\Controllers\Controller;
use App\Services\Store\OrderService;

class OrderController extends Controller
{
    /**
     * The order service Instanse
     *
     * @var App\Services\Store\OrderService
     */
    protected $orderService = null;

    public function __construct (OrderService $orderService)
    {
        parent::__construct();

        $this->orderService = $orderService;
    }

    /**
     * Get all orders
     *
     * @param Request $request The Request instance
     *
     * @return Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function index (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'integer',
            'items' => 'integer',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $page = $request->get('page') ?? 1;
        $items = $request->get('items') ?? 15;

        $orders = $this->orderService->index(
            (int)$page,
            (int)$items
        );

        $resource = new Collection(
            $orders->getCollection(),
            new OrderTransformer(),
            Order::RESOURCE_KEY
        );

        $this->fractal->parseIncludes([
            User::RESOURCE_KEY,
            Product::RESOURCE_KEY,
        ]);
        $resource->setPaginator(new IlluminatePaginatorAdapter($orders));

        $response = $this->fractal->createData($resource)->toArray();

        return response()->json($response);
    }

    /**
     * This function changes order status status
     *
     * @param Request $request The Request instance
     * @param int     $orderId The id of order
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function changeStatus(Request $request, $orderId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:'.implode(',', array_values( Order::STATUSES)),
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $order = $this->orderService->changeStatus(
            $orderId,
            $request->get('status')
        );

        return response()->json([
            'success' => true,
            'message' => "Order No:{$order->id} status has been changed",
        ]);
    }
}
