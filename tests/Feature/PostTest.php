<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Laravel;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('can create a post successfully', function () {
    $user = User::factory()->create();
    actingAs($user);

    $categories = Category::factory()->count(2)->create();
    $categoriesId = $categories->pluck('id')->toArray();
    $response = $this->postJson('/api/v1/posts', [
        'title' => 'Mi nueva publicación',
        'excerpt' => 'Lorem ipsum sit amet',
        'content' => 'Lorem ipsum dolor sit amet...',
        'categories' => $categoriesId,
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'id',
            'title',
            'slug',
            'excerpt',
            'content',
            'categories' => [
                '*' => ['id', 'name'],
            ],
            'user' => ['id', 'name', 'email'],
            'created_at',
            'updated_at',
        ]);
});

it('generates a unique slug from the title', function () {
    $title = 'Mi título de prueba';
    $slug = \App\Models\Post::generateUniqueSlug($title);
    expect($slug)->toBe('mi-titulo-de-prueba');
});

it('generates a unique slug even if the title is repeated', function () {
    $user = User::factory()->create(); // Crea un usuario

    $title = 'Título repetido';
    \App\Models\Post::create(['title' => $title, 'excerpt' => 'test', 'content' => 'test', 'user_id' => $user->id]); // Usa el id del usuario creado
    $slug = \App\Models\Post::generateUniqueSlug($title);
    expect($slug)->toBe('titulo-repetido-1');

    \App\Models\Post::create(['title' => $title, 'excerpt' => 'test', 'content' => 'test', 'user_id' => $user->id]); // Usa el id del usuario creado
    $slug2 = \App\Models\Post::generateUniqueSlug($title);
    expect($slug2)->toBe('titulo-repetido-2');
});

it('can list posts successfully', function () {
    $user = User::factory()->create();
    actingAs($user);

    Post::factory()->count(3)->create(['user_id' => $user->id]);

    $response = $this->getJson('/api/v1/posts');

    $response->assertStatus(200)
        ->assertJsonCount(3);
});

it('can filter posts by search term', function () {
    $user = User::factory()->create();
    actingAs($user);

    Post::factory()->create([
        'title' => 'Publicación de prueba',
        'content' => 'Contenido de prueba',
        'user_id' => $user->id
    ]);
    Post::factory()->create([
        'title' => 'Otra publicación',
        'content' => 'Otro contenido',
        'user_id' => $user->id
    ]);

    $response = $this->getJson('/api/v1/posts?search=prueba');

    $response->assertStatus(200)
        ->assertJsonCount(1);
});

it('returns an authentication error if the user is not authenticated', function () {
    $response = $this->postJson('/api/v1/posts', [
        'title' => 'Mi nueva publicación',
        'excerpt' => 'Lorem ipsum sit amet',
        'content' => 'Lorem ipsum dolor sit amet...',
        'categories' => [1, 2],
    ]);

    $response->assertStatus(401);
});

it('returns a validation error if required fields are missing', function () {
    $user = User::factory()->create();
    actingAs($user);

    $response = $this->postJson('/api/v1/posts', [
        'title' => 'Mi nueva publicación',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['excerpt', 'content', 'categories']);
});