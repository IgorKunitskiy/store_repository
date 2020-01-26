<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;

class OrdersTest extends TestCase
{
    private $user = null;

    private $token = null;

    private $jsonStructure = [
        'data' => [
            [
                'id',
                'status',
                'users' => [
                    'data' => [
                        'id',
                        'name',
                    ],
                ],
                'products' => [
                    'data' => [
                        [
                            'id',
                            'name',
                        ]
                    ]
                ],
            ],
        ],
        'meta' => [
            'pagination' => [
                'total',
                'count',
                'per_page',
                'current_page',
                'total_pages',
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->token = Str::random(60);
        $this->user = factory(User::class)->create([
            'role' => 'admin',
            'api_token' =>  hash('sha256', $this->token),
        ]);

        $this->be($this->user, 'api');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIndexOrdersTest()
    {
        $products = factory(Product::class, 30)->create();

        factory(Order::class, 30)->create()
            ->each(function(Order $order) use ($products) {
                $order->products()->saveMany($products->random(rand(1, 5)));
            });

        $response = $this->json('GET', 'api/orders', [
            'page' => 2,
            'items' => 15,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure($this->jsonStructure);
    }
}
