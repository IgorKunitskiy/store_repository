<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    /**
     * The League\Fractal Product model resorce key
     *
     * @var string
     */
    const RESOURCE_KEY = 'products';

    /**
     * Products relation maping
     *
     * @return array of Order model instanses
     */
    public function orders()
    {
        return $this->belongsToMany('App\Models\Order', 'order_products');
    }
}
