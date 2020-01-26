<?php

namespace App\Transformers;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use League\Fractal;

class OrderTransformer extends Fractal\TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        User::RESOURCE_KEY,
        Product::RESOURCE_KEY,
    ];

	public function transform(Order $order)
	{
	    return [
	        'id' => (int) $order->id,
	        'status' => $order->status,
	    ];
	}

	public function includeUsers(Order $order)
    {
	    return $this->item(
            $order->user,
            new UserTransformer(),
            User::RESOURCE_KEY
        );
    }

    public function includeProducts(Order $order)
    {
        return $this->collection(
            $order->products,
            new ProductTransformer(),
            Product::RESOURCE_KEY
        );
    }
}
