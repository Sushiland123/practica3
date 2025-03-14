<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Laravel;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('can create a post successfully even with duplicate slugs', function () {
    $user = User::factory()->create();
    actingAs($user);

    // Crear la primera publicación
    $post1 = Post::create([
        'title' => 'Título repetido',
        'excerpt' => 'Extracto de prueba 1',
        'content' => 'Contenido de prueba 1',
        'user_id' => $user->id,
    ]);

    // Crear la segunda publicación con el mismo título
    $post2 = Post::create([
        'title' => 'Título repetido',
        'excerpt' => 'Extracto de prueba 2',
        'content' => 'Contenido de prueba 2',
        'user_id' => $user->id,
    ]);

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