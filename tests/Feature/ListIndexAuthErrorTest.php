<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

it('returns authentication error when trying to list posts with filter without being authenticated', function () {
    // Send a GET request to list filtered posts without authentication
    $response = getJson('/api/v1/posts?search=Filtered Post');

    // Assert that the response status is 401 Unauthorized
    $response->assertStatus(401);
});