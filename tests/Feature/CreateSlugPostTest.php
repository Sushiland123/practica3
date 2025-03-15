<?php

use App\Models\Post;
use App\Models\User;
use App\Models\Category; // Importa el modelo Category
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreatePostSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_slug_is_created_successfully_when_post_is_created(): void
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

        $createdPost = Post::where('title', 'Test Post Title')->first();

        // Verificar que el slug se creó correctamente
        $this->assertEquals(Str::slug('Test Post Title'), $createdPost->slug);

        // Verificar que el post se guardó en la base de datos con el slug correcto
        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post Title',
            'slug' => Str::slug('Test Post Title'),
            'excerpt' => 'Test post excerpt',
            'content' => 'Test post content',
            'user_id' => $user->id,
        ]);

        // Verificar que las categorías se asignaron correctamente (en la tabla pivot)
        $this->assertDatabaseHas('category_post', [
            'post_id' => $createdPost->id,
            'category_id' => $categoryIds[0],
        ]);
        $this->assertDatabaseHas('category_post', [
            'post_id' => $createdPost->id,
            'category_id' => $categoryIds[1],
        ]);
    }
}