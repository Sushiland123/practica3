<?php

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

it('can list all posts of a user with filter', function () {
    // Create a user
    $user = User::factory()->create();

    // Create some posts for the user
    Post::factory()->count(5)->create(['user_id' => $user->id, 'title' => 'Filtered Post']);
    Post::factory()->count(5)->create(['user_id' => $user->id, 'title' => 'Other Post']);

    // Act as the user and send a GET request to list filtered posts
    $response = $this->actingAs($user)->getJson('/api/v1/posts?search=Filtered Post');

    // Assert that the response is successful
    $response->assertStatus(200);

    // Assert that the response contains only the filtered posts
    $response->assertJsonCount(5);

    $response->assertJsonstructure([
         [
            'id',
            'title',
            'excerpt',
            'content',
            'user_id',
            'created_at',
            'updated_at',
        ],
    ]);
});