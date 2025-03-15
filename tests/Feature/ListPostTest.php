<?php

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('can list all posts of a user without filter', function () {
    // Create a user
    $user = User::factory()->create();

    // Create some posts for the user
    Post::factory()->count(10)->create(['user_id' => $user->id]);

    // Act as the user and send a GET request to list posts
    $response = $this->actingAs($user)->GETJson('/api/v1/posts');

    // Assert that the response is successful
    $response->assertStatus(200);

    // Assert that the response contains the posts
    $response->assertJsonCount(10);
});