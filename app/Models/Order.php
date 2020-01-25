<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    /**
     * @const STATUSES array of posible order status
     */
    const STATUSES = [
        'pending' => 'pending',
        'done' => 'done',
        'declined' => 'declined',
    ];

    /**
     * User relation maping
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Products relation maping
     *
     * @return array of Product model instanses
     */
    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'order_products');
    }
}
