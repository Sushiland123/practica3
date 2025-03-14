<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('returns an authentication error if the user is not authenticated', function () {
    $response = postJson('/api/v1/posts', [
        'title' => 'Publicación sin autenticación',
        'excerpt' => 'Extracto de prueba',
        'content' => 'Contenido de prueba',
    ]);

    $response->assertStatus(401)
             ->assertJson([
                 'message' => 'Unauthenticated.',
             ]);
});