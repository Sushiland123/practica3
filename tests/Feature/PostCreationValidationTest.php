<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class PostCreationValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_creation_requires_all_fields(): void
    {
        // 1. Arrange
        $user = User::factory()->create();
        Auth::login($user);

        // Data with missing 'title'
        $postDataMissingTitle = [
            'excerpt' => 'Test excerpt',
            'content' => 'Test content',
            'categories' =>'', // Corrected: Assign an empty array
        ];

        // Data with missing 'excerpt'
        $postDataMissingExcerpt = [
            'title' => 'Test title',
            'content' => 'Test content',
            'categories' => '', // Corrected: Assign an empty array
        ];

        // Data with missing 'content'
        $postDataMissingContent = [
            'title' => 'Test title',
            'excerpt' => 'Test excerpt',
            'categories' => '', // Corrected: Assign an empty array
        ];

        // Data with missing 'categories'
        $postDataMissingCategories = [
            'title' => 'Test title',
            'excerpt' => 'Test excerpt',
            'content' => 'Test content',
        ];

        // 2. Act & Assert
        // Missing title
        $response = $this->actingAs($user)->postJson('/api/v1/posts', $postDataMissingTitle);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);

        // Missing excerpt
        $response = $this->actingAs($user)->postJson('/api/v1/posts', $postDataMissingExcerpt);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['excerpt']);

        // Missing content
        $response = $this->actingAs($user)->postJson('/api/v1/posts', $postDataMissingContent);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['content']);

        // Missing categories
        $response = $this->actingAs($user)->postJson('/api/v1/posts', $postDataMissingCategories);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['categories']);
    }
}