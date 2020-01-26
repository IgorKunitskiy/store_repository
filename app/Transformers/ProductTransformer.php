<?php

namespace App\Transformers;

use App\Models\Product;
use League\Fractal;

class ProductTransformer extends Fractal\TransformerAbstract
{
	public function transform(Product $product)
	{
	    return [
	        'id' => (int) $product->id,
	        'name' => $product->name,
	    ];
	}

}
