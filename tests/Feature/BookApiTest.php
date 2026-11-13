<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    private function login(): User
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        return $user;
    }

    private function bookPayload(array $override = []): array
    {
        return array_merge([
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'publisher' => 'Bentang Pustaka',
            'isbn' => '9789793062792',
            'published_year' => 2005,
            'stock' => 10,
            'description' => 'Novel tentang anak-anak Belitung.',
        ], $override);
    }

    public function test_endpoint_buku_menolak_akses_tanpa_login(): void
    {
        $this->getJson('/api/books')->assertUnauthorized();
        $this->postJson('/api/books', $this->bookPayload())->assertUnauthorized();
    }

    public function test_daftar_buku_berpaginasi_dan_bisa_dicari(): void
    {
        $this->login();
        Book::factory(15)->create();
        Book::factory()->create(['title' => 'Bumi Manusia', 'author' => 'Pramoedya Ananta Toer']);

        $this->getJson('/api/books?per_page=10')
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.total', 16)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonStructure(['data', 'links', 'meta']);

        $this->getJson('/api/books?search=Pramoedya')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Bumi Manusia');
    }

    public function test_tambah_buku_dan_validasi(): void
    {
        $this->login();

        $this->postJson('/api/books', $this->bookPayload())
            ->assertCreated()
            ->assertJsonPath('data.title', 'Laskar Pelangi')
            ->assertJsonPath('data.stock', 10);

        $this->assertDatabaseHas('books', ['isbn' => '9789793062792']);

        // ISBN duplikat
        $this->postJson('/api/books', $this->bookPayload())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['isbn']);

        // Field wajib kosong
        $this->postJson('/api/books', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'author', 'isbn', 'published_year', 'stock']);
    }

    public function test_lihat_detail_dan_ubah_buku(): void
    {
        $this->login();
        $book = Book::factory()->create(['stock' => 3]);

        $this->getJson("/api/books/{$book->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $book->id);

        // PATCH sebagian field
        $this->patchJson("/api/books/{$book->id}", ['stock' => 25])
            ->assertOk()
            ->assertJsonPath('data.stock', 25)
            ->assertJsonPath('data.title', $book->title);

        // PUT dengan ISBN milik sendiri tetap lolos validasi unique
        $this->putJson("/api/books/{$book->id}", ['isbn' => $book->isbn, 'title' => 'Judul Baru'])
            ->assertOk()
            ->assertJsonPath('data.title', 'Judul Baru');

        $this->getJson('/api/books/999999')->assertNotFound();
    }

    public function test_hapus_buku(): void
    {
        $this->login();
        $book = Book::factory()->create();

        $this->deleteJson("/api/books/{$book->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Buku berhasil dihapus');

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
        $this->deleteJson("/api/books/{$book->id}")->assertNotFound();
    }
}
