<?php

use App\Models\Post;
use App\Models\User;
use App\Models\Category; // Importa el modelo Category
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class PostCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_post_with_categories(): void
    {
        // 1. Arrange
        $user = User::factory()->create();
        Auth::login($user);

        // Crear algunas categorías para asignar al post
        $categories = Category::factory()->count(2)->create();
        $categoryIds = $categories->pluck('id')->toArray();

        $postData = [
            'title' => 'Test Post Title',
            'excerpt' => 'Test post excerpt',
            'content' => 'Test post content',
            'categories' => $categoryIds, // Usar los IDs de las categorías creadas
        ];

        // 2. Act
        $response = $this->actingAs($user)->postJson('/api/v1/posts', $postData);

        // 3. Assert
        $response->assertStatus(201);

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post Title',
            'excerpt' => 'Test post excerpt',
            'content' => 'Test post content',
            'user_id' => $user->id,
        ]);

        // Verificar que las categorías se asignaron correctamente (en la tabla pivot)
        $this->assertDatabaseHas('category_post', [
            'post_id' => Post::where('title', 'Test Post Title')->first()->id,
            'category_id' => $categoryIds[0],
        ]);
        $this->assertDatabaseHas('category_post', [
            'post_id' => Post::where('title', 'Test Post Title')->first()->id,
            'category_id' => $categoryIds[1],
        ]);
    }
}