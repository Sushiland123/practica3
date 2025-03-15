<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    /**
     * Almacena un nuevo post.
     *
     * @param  StorePostRequest  $request
     * @return JsonResponse
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        // Crea el post con los datos del request y el user_id del usuario autenticado
        $post = Post::create([
            'title' => $request->title,
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'user_id' => auth()->id(),
        ]);

        // Asocia las categorías al post usando la tabla pivot
        $post->categories()->attach($request->categories);

        // Carga las relaciones 'categories' y 'user' para incluirlas en la respuesta
        $post->load('categories', 'user');

        // Retorna el post creado con un código de estado 201 (Created)
        return response()->json(new PostResource($post), 201);
    }

    /**
     * Lista los posts del usuario autenticado.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // Obtiene el término de búsqueda del request (si existe)
        $search = $request->input('search');

        // Obtiene los posts del usuario autenticado, aplicando el filtro de búsqueda (si existe)
        $posts = Post::where('user_id', auth()->id())
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            })
            ->with('categories', 'user') // Carga las relaciones
            ->get();

        // Retorna la lista de posts
        return response()->json($posts);
    }
}