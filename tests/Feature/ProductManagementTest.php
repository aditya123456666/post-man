<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_product_api_supports_search_and_price_filters(): void
    {
        Product::factory()->create([
            'name' => 'Mouse Gaming',
            'price' => 250000,
            'stock' => 4,
        ]);
        Product::factory()->create([
            'name' => 'Mouse Premium',
            'price' => 750000,
            'stock' => 0,
        ]);
        Product::factory()->create([
            'name' => 'Keyboard Mechanical',
            'price' => 300000,
            'stock' => 8,
        ]);

        $response = $this->getJson('/api/products?search=Mouse&min_price=200000&max_price=500000&in_stock=1');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Mouse Gaming');
    }

    public function test_product_mutation_requires_an_administrator(): void
    {
        $payload = [
            'name' => 'Produk Baru',
            'price' => 100000,
            'stock' => 2,
        ];

        $this->postJson('/api/products', $payload)->assertUnauthorized();

        $customer = User::factory()->create();

        $this->actingAs($customer, 'sanctum')
            ->postJson('/api/products', $payload)
            ->assertForbidden();

        $this->assertDatabaseCount('products', 0);
    }

    public function test_administrator_can_create_product_with_an_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin, 'sanctum')
            ->post('/api/products', [
                'name' => 'Laptop Test',
                'description' => 'Laptop untuk pengujian.',
                'price' => 12500000,
                'stock' => 3,
                'image' => UploadedFile::fake()->image('laptop.jpg'),
            ], ['Accept' => 'application/json']);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Laptop Test')
            ->assertJsonPath('data.stock', 3);

        $product = Product::firstOrFail();
        $this->assertModelExists($product);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_invalid_product_input_is_rejected_without_persisting_data(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/products', [
                'name' => '',
                'price' => -1,
                'stock' => -5,
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'price', 'stock']);
        $this->assertDatabaseCount('products', 0);
    }

    public function test_product_used_in_an_order_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $customer = User::factory()->create();
        $product = Product::factory()->create();
        $order = Order::create([
            'user_id' => $customer->id,
            'total_price' => $product->price,
            'status' => 'pending',
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => $product->price,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->deleteJson('/api/products/'.$product->id)
            ->assertConflict();

        $this->assertModelExists($product);
        $this->assertDatabaseHas('order_items', ['order_id' => $order->id]);
    }

    public function test_admin_dashboard_requires_an_admin_session(): void
    {
        $this->get('/admin')->assertRedirect('/login');

        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_api_registration_issues_a_token_and_logout_revokes_it(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'API Customer',
            'email' => 'customer@example.test',
            'password' => 'StrongPassword123',
            'password_confirmation' => 'StrongPassword123',
            'is_admin' => true,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.email', 'customer@example.test');

        $token = $response->json('access_token');
        $this->assertIsString($token);

        $this->withToken($token)
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('data.is_admin', false);
        $this->withToken($token)->postJson('/api/logout')->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_customer_can_checkout_and_only_sees_their_own_order_history(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 250000,
            'stock' => 3,
        ]);

        $this->actingAs($customer, 'sanctum')
            ->postJson('/api/orders/checkout', [
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 2],
                ],
                'shipping_address' => 'Jl. Merdeka No. 1, Jakarta Pusat',
            ])
            ->assertCreated()
            ->assertJsonPath('data.items.0.quantity', 2)
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.shipping_address', 'Jl. Merdeka No. 1, Jakarta Pusat');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
        $this->assertDatabaseHas('orders', ['user_id' => $customer->id]);

        $this->actingAs(User::factory()->create(), 'sanctum')
            ->getJson('/api/orders/history')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_checkout_rolls_back_when_stock_is_insufficient(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create(['stock' => 1]);

        $this->actingAs($customer, 'sanctum')
            ->postJson('/api/orders/checkout', [
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 2],
                ],
                'shipping_address' => 'Jl. Merdeka No. 1, Jakarta Pusat',
            ])
            ->assertUnprocessable();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_admin_can_sign_in_to_the_web_dashboard(): void
    {
        User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'StrongPassword123',
            'is_admin' => true,
        ]);

        $this->post('/login', [
            'email' => 'admin@example.test',
            'password' => 'StrongPassword123',
        ])->assertRedirect(route('admin.products.index'));

        $this->get('/admin')->assertOk();
    }

    public function test_customer_can_register_and_checkout_their_cart_as_cod(): void
    {
        $product = Product::factory()->create([
            'name' => 'Keyboard COD',
            'price' => 450000,
            'stock' => 5,
            'image' => 'products/keyboard-mechanical.svg',
        ]);

        $this->post('/cart/'.$product->id, ['quantity' => 2])
            ->assertRedirect(route('cart.index'));

        $this->post('/register', [
            'name' => 'Pelanggan Baru',
            'email' => 'cod-customer@example.test',
            'password' => 'StrongPassword123',
            'password_confirmation' => 'StrongPassword123',
            'is_admin' => true,
        ])->assertRedirect(route('checkout.create'));

        $customer = User::where('email', 'cod-customer@example.test')->firstOrFail();
        $this->assertFalse($customer->is_admin);

        $this->get('/checkout')->assertOk();

        $response = $this->post('/checkout', [
            'shipping_address' => 'Jl. Merdeka No. 1, Jakarta Pusat',
        ]);
        $order = Order::firstOrFail();

        $response->assertRedirect(route('orders.show', $order));
        $this->assertSame('pending', $order->status);
        $this->assertSame('Jl. Merdeka No. 1, Jakarta Pusat', $order->shipping_address);
        $this->assertSame('900000.00', $order->total_price);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => '450000.00',
        ]);

        $this->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('Menunggu konfirmasi')
            ->assertSee('COD')
            ->assertSee('Jl. Merdeka No. 1, Jakarta Pusat');
    }

    public function test_customer_can_sign_in_without_receiving_admin_access(): void
    {
        User::factory()->create([
            'email' => 'customer-login@example.test',
            'password' => 'StrongPassword123',
            'is_admin' => false,
        ]);

        $this->post('/login', [
            'email' => 'customer-login@example.test',
            'password' => 'StrongPassword123',
        ])->assertRedirect(route('shop'));

        $this->get('/admin')->assertForbidden();
    }

    public function test_web_checkout_keeps_the_cart_when_stock_is_insufficient(): void
    {
        $customer = User::factory()->create();
        $product = Product::factory()->create(['stock' => 1]);

        $this->withSession(['cart' => [(string) $product->id => 2]])
            ->actingAs($customer)
            ->post('/checkout', [
                'shipping_address' => 'Jl. Merdeka No. 1, Jakarta Pusat',
            ])
            ->assertSessionHasErrors('cart');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 1]);
        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(2, session('cart')[(string) $product->id]);
    }

    public function test_customer_cannot_view_another_customers_order(): void
    {
        $owner = User::factory()->create();
        $customer = User::factory()->create();
        $order = Order::create([
            'user_id' => $owner->id,
            'total_price' => 1000,
            'status' => 'pending',
        ]);

        $this->actingAs($customer)
            ->get(route('orders.show', $order))
            ->assertNotFound();
    }

    public function test_product_seeder_assigns_a_local_illustration_to_every_seeded_product(): void
    {
        $this->seed(ProductSeeder::class);

        $products = Product::query()->get();

        $this->assertCount(10, $products);

        foreach ($products as $product) {
            $this->assertNotNull($product->image);
            Storage::disk('public')->assertExists($product->image);
        }
    }
}
