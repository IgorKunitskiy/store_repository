<?php

namespace Tests\Feature;

use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Notifications\OrderStatusChanged;

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

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure($this->jsonStructure);
    }

    public function testChangeOrderStatusTest()
    {
        Notification::fake();
        $order = factory(Order::class)->create([
            'status' => Order::STATUSES['pending'],
        ]);

        $response = $this->put("api/orders/{$order->id}", [
            'status' => Order::STATUSES['done'],
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'success',
            'message',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUSES['done'],
        ]);

        Notification::assertSentTo($this->user, OrderStatusChanged::class);
    }

    public function testChangeOrderStatusIncorrectStatusTest()
    {
        $order = factory(Order::class)->create([
            'status' => Order::STATUSES['pending'],
        ]);

        $response = $this->put("api/orders/{$order->id}", [
            'status' => Order::STATUSES['done']."dummy",
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testChangeOrderStatusExistedStatusTest()
    {
        $order = factory(Order::class)->create([
            'status' => Order::STATUSES['pending'],
        ]);

        $response = $this->put("api/orders/{$order->id}", [
            'status' => Order::STATUSES['pending'],
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
