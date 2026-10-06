<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $override = []): array
    {
        return array_merge([
            'category_id' => Category::factory()->create()->id,
            'title'       => 'Produk Uji',
            'description' => 'Deskripsi uji',
            'price'       => 150000,
            'stock'       => 5,
        ], $override);
    }

    public function test_catalog_requires_login(): void
{
    $product = Product::factory()->create();

    $this->get('/products')->assertRedirect(route('login'));
    $this->get("/products/{$product->id}")->assertRedirect(route('login'));

    $user = User::factory()->create();
    $this->actingAs($user)->get('/products')->assertOk();
    $this->actingAs($user)->get("/products/{$product->id}")->assertOk();
}

    public function test_guest_is_redirected_to_login_for_management_routes(): void
    {
        $this->get('/products/create')->assertRedirect(route('login'));
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_user_role_cannot_manage_products(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user)->get('/products/create')->assertForbidden();
        $this->actingAs($user)->post('/products', $this->payload())->assertForbidden();
        $this->actingAs($user)->get("/products/{$product->id}/edit")->assertForbidden();
        $this->actingAs($user)->delete("/products/{$product->id}")->assertForbidden();
    }

    public function test_editor_can_create_and_edit_own_product_only(): void
    {
        $editor = User::factory()->editor()->create();
        $own = Product::factory()->create(['user_id' => $editor->id]);
        $others = Product::factory()->create();

        $this->actingAs($editor)->get('/products/create')->assertOk();
        $this->actingAs($editor)->post('/products', $this->payload())->assertRedirect();
        $this->assertDatabaseHas('products', ['title' => 'Produk Uji', 'user_id' => $editor->id]);

        $this->actingAs($editor)->get("/products/{$own->id}/edit")->assertOk();
        $this->actingAs($editor)->get("/products/{$others->id}/edit")->assertForbidden();
        $this->actingAs($editor)->put("/products/{$others->id}", $this->payload())->assertForbidden();
    }

    public function test_editor_cannot_delete_but_admin_can(): void
    {
        $editor = User::factory()->editor()->create();
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['user_id' => $editor->id]);

        $this->actingAs($editor)->delete("/products/{$product->id}")->assertForbidden();
        $this->assertDatabaseHas('products', ['id' => $product->id]);

        $this->actingAs($admin)->delete("/products/{$product->id}")->assertRedirect(route('products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_area_is_admin_only(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
        $this->actingAs(User::factory()->editor()->create())->get('/admin')->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())->get('/admin')->assertOk();
    }

    public function test_validation_rejects_invalid_product(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/products', $this->payload(['title' => '', 'price' => 'abc', 'stock' => -1]))
            ->assertSessionHasErrors(['title', 'price', 'stock']);
    }

    public function test_role_cannot_be_mass_assigned(): void
    {
        $user = User::create(['name' => 'X', 'email' => 'x@example.com', 'password' => 'secret123', 'role' => 'admin']);
        $this->assertSame('user', $user->fresh()->role);
    }

    public function test_in_stock_scope(): void
    {
        Product::factory()->create(['stock' => 3]);
        Product::factory()->outOfStock()->create();

        $this->assertSame(1, Product::inStock()->count());
    }
}
