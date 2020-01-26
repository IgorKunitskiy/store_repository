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
     * @return Illuminate\Http\JsonResponse
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
}
