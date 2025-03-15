<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Laravel;
use function Pest\Laravel\actingAs;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('can create a post successfully via API even with duplicate slugs', function () {
    $user = User::factory()->create();
    actingAs($user);

                // Crear algunas categorías para asignar al post
                $categories = Category::factory()->count(2)->create();
                $categoryIds = $categories->pluck('id')->toArray();

    // Crear la primera publicación a través de la API
    $response1 = $this->postJson('/api/v1/posts', [
        'title' => 'Título repetido',
        'excerpt' => 'Extracto de prueba 1',
        'content' => 'Contenido de prueba 1',
        'categories' => $categoryIds,
    ]);


    $response1->assertStatus(201) // Asegura que la respuesta es 201 Created
             ->assertJsonstructure([
                     'title',
                     'excerpt',
                     'content',
                     'slug',
                     'user_id',
             ]);

    $post1 = Post::find($response1->json('id'));

    // Crear la segunda publicación con el mismo título a través de la API
    $response2 = $this->postJson('/api/v1/posts', [
        'title' => 'Título repetido',
        'excerpt' => 'Extracto de prueba 2',
        'content' => 'Contenido de prueba 2',
        'user_id' => $user->id,
        'categories' => $categoryIds,


    ]);

    $response2->assertStatus(201) // Asegura que la respuesta es 201 Created
             ->assertJsonstructure([
                     'title',
                     'excerpt', 
                     'content', 
                     'slug',
                     'user_id',
                 
             ]);

    $post2 = Post::find($response2->json('id'));

    // Asegurarse de que ambas publicaciones fueron creadas exitosamente y tienen slugs únicos
    expect($post1->slug)->toBe('titulo-repetido');
    expect($post2->slug)->toBe('titulo-repetido-1');

    // Verificar que la respuesta sea correcta y contenga la estructura esperada
    $this->assertDatabaseHas('posts', [
        'id' => $post1->id,
        'slug' => 'titulo-repetido'
    ]);

    $this->assertDatabaseHas('posts', [
        'id' => $post2->id,
        'slug' => 'titulo-repetido-1'
    ]);
});