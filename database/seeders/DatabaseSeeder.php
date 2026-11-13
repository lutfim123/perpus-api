<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun demo untuk login di Swagger UI
        User::factory()->create([
            'name' => 'Admin Perpustakaan',
            'email' => 'admin@example.com',
            'password' => 'password', // otomatis di-hash oleh cast 'hashed'
        ]);

        // 20 buku contoh
        Book::factory(20)->create();
    }
}
