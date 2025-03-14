<?php

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event; // Import the Event facade

class PostCreationWithDuplicateSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_post_with_duplicate_slug(): void
    {
        // 1. Arrange
        $user = User::factory()->create();
        Auth::login($user);

        // Crear una categoría
        $category = Category::factory()->create();
        $categoryIds = [$category->id];

        $postTitle = 'Test Post Title';
        $firstPostSlug = Str::slug($postTitle); // Generate the base slug

        // Crear un post inicial con el título que usaremos de "duplicado"
        Event::fake(); // Prevent all events from firing
        Post::create([
            'title' => $postTitle,
            'slug' => $firstPostSlug, // Assign the slug directly
            'excerpt' => 'Initial excerpt',
            'content' => 'Initial content',
            'user_id' => $user->id,
        ]);
        Event::clearResolvedInstances(); // Clear resolved instances

        // Debug: Get and output the slug of the first post
        $firstPost = Post::where('title', $postTitle)->first();
        dump("First Post Slug: " . $firstPost->slug);

        // Data for the new post (same title, will generate a duplicate slug initially)
        $postData = [
            'title' => $postTitle,
            'excerpt' => 'New post excerpt',
            'content' => 'New post content',
            'categories' => $categoryIds,
        ];

        // 2. Act
        $response = $this->actingAs($user)->postJson('/api/v1/posts', $postData);

        // 3. Assert
        $response->assertStatus(201);

        // Verificar que el post se creó en la base de datos
        $this->assertDatabaseHas('posts', [
            'title' => $postTitle,
            'excerpt' => 'New post excerpt',
            'content' => 'New post content',
            'user_id' => $user->id,
        ]);

        // Get the newly created post
        $newPost = Post::where('title', $postTitle)->latest()->first();

        // Debug: Get and output the slug of the new post
        dump("New Post Slug: " . $newPost->slug);

        // Verificar que el slug no es el mismo que el del primer post
        $this->assertNotEquals($firstPostSlug, $newPost->slug);

        // Verificar que el slug tiene el sufijo incremental
        $this->assertStringContainsString($firstPostSlug . '-', $newPost->slug);
    }
}