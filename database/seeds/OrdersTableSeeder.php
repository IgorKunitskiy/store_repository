<?php

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;

class OrdersTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::all();
        // $products = ;
        factory(Order::class, 10)->create()
            ->each(function(Order $order) use ($products) {
                $order->products()->saveMany($products->random(rand(1, 5)));
            });
    }
}
