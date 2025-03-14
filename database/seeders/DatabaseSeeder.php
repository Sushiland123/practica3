<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear usuarios
        DB::table('users')->insert([
            'nombre' => 'John Doe',
            'correo' => 'john.doe@example.org',
            'nombreUsuario' => 'johndoe',
            'edad' => 30,
            'país' => 'EE.UU.',
            'password' => Hash::make('password'), // contraseña
            'remember_token' => Str::random(10),
        ]);

        // Crear categorías
        DB::table('categories')->insert([
            ['name' => 'noticias'],
            ['name' => 'tutorial'],
            ['name' => 'demo'],
        ]);

        // Crear posts
        DB::table('posts')->insert([
            'title' => 'Mi nueva publicación',
            'slug' => 'mi-nueva-publicacion',
            'excerpt' => 'Lorem ipsum sit amet',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
            'user_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Asignar categorías al post (tabla pivot)
        DB::table('category_post')->insert([
            ['post_id' => 1, 'category_id' => 1],
            ['post_id' => 1, 'category_id' => 3],
        ]);
    }
}