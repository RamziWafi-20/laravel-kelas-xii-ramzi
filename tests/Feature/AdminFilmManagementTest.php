<?php

namespace Tests\Feature;

use App\Models\Cast;
use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use App\Models\Peran;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminFilmManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_routes_reject_guests_and_non_admin_users(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.films.index'))->assertRedirect(route('login'));

        $this->actingAs($this->createUser('user', 'regular@example.com'))
            ->get(route('admin.dashboard'))
            ->assertForbidden();
        $this->actingAs($this->createUser('user', 'another@example.com'))
            ->post(route('admin.films.store'), [])
            ->assertForbidden();
    }

    public function test_admin_can_create_update_and_delete_a_film(): void
    {
        $admin = $this->createUser('admin', 'admin-crud@example.com');
        $genre = Genre::query()->firstOrFail();
        $attributes = [
            'title' => 'Admin CRUD Film',
            'sinopsis' => 'Film description for CRUD test',
            'year' => 2024,
            'poster' => 'storage/images/m1.jpg',
            'genre_id' => $genre->id,
        ];

        $this->actingAs($admin)
            ->post(route('admin.films.store'), $attributes)
            ->assertRedirect(route('admin.films.index'))
            ->assertSessionHas('status', 'Film berhasil ditambahkan.');

        $film = Film::where('title', 'Admin CRUD Film')->firstOrFail();
        $this->assertDatabaseHas('films', $attributes);

        $this->put(route('admin.films.update', $film), [
            ...$attributes,
            'title' => 'Updated Admin CRUD Film',
        ])->assertRedirect(route('admin.films.index'))
            ->assertSessionHas('status', 'Film berhasil diperbarui.');
        $this->assertDatabaseHas('films', ['id' => $film->id, 'title' => 'Updated Admin CRUD Film']);

        $cast = Cast::create(['name' => 'Delete Test Cast', 'age' => 30, 'bio' => 'Test']);
        Peran::create(['film_id' => $film->id, 'cast_id' => $cast->id, 'actor' => 'Character']);
        Kritik::create([
            'comment' => 'Comment removed with its film',
            'rating' => 0,
            'film_id' => $film->id,
            'user_id' => $admin->id,
        ]);

        $this->delete(route('admin.films.destroy', $film))
            ->assertRedirect(route('admin.films.index'))
            ->assertSessionHas('status', 'Film berhasil dihapus.');
        $this->assertDatabaseMissing('films', ['id' => $film->id]);
        $this->assertDatabaseMissing('perans', ['film_id' => $film->id]);
        $this->assertDatabaseMissing('kritiks', ['film_id' => $film->id]);
    }

    public function test_film_admin_list_supports_search_and_pagination(): void
    {
        $admin = $this->createUser('admin', 'admin-list@example.com');
        $genre = Genre::query()->firstOrFail();
        foreach (range(1, 16) as $number) {
            Film::create([
                'title' => 'Admin Search Film ' . $number,
                'sinopsis' => 'Film description',
                'year' => 2020,
                'poster' => 'storage/images/m1.jpg',
                'genre_id' => $genre->id,
            ]);
        }

        $this->actingAs($admin)
            ->get(route('admin.films.index'))
            ->assertOk()
            ->assertSee('Admin Search Film 1')
            ->assertSee('pagination');

        $this->get(route('admin.films.index', ['search' => 'Admin Search Film 16']))
            ->assertOk()
            ->assertSee('Admin Search Film 16')
            ->assertDontSee('Admin Search Film 15');
    }

    public function test_admin_film_form_displays_validation_errors(): void
    {
        $admin = $this->createUser('admin', 'admin-validation@example.com');

        $this->actingAs($admin)
            ->from(route('admin.films.create'))
            ->post(route('admin.films.store'), [
                'title' => '',
                'sinopsis' => '',
                'year' => 'not-a-year',
                'poster' => '',
                'genre_id' => 999999,
            ])
            ->assertRedirect(route('admin.films.create'))
            ->assertSessionHasErrors(['title', 'sinopsis', 'year', 'poster', 'genre_id']);
    }

    public function test_admin_can_delete_a_comment_from_the_dashboard(): void
    {
        $admin = $this->createUser('admin', 'admin-comments@example.com');
        $film = Film::query()->firstOrFail();
        $comment = Kritik::create([
            'comment' => 'Admin can remove this comment',
            'rating' => 0,
            'film_id' => $film->id,
            'user_id' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.comments.destroy', $comment))
            ->assertRedirect(route('admin.comments.index'))
            ->assertSessionHas('status', 'Komentar berhasil dihapus.');

        $this->assertDatabaseMissing('kritiks', ['id' => $comment->id]);
    }

    public function test_comments_have_a_separate_admin_page(): void
    {
        $admin = $this->createUser('admin', 'admin-comments-page@example.com');
        $film = Film::query()->firstOrFail();
        Kritik::create([
            'comment' => 'Visible on comments management page',
            'rating' => 0,
            'film_id' => $film->id,
            'user_id' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.comments.index'))
            ->assertOk()
            ->assertSee('Kelola Komentar')
            ->assertSee('Visible on comments management page');
    }

    public function test_admin_can_manage_cast_and_actor_roles(): void
    {
        $admin = $this->createUser('admin', 'admin-cast-actor@example.com');
        $film = Film::query()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.casts.store'), [
                'name' => 'Manageable Cast',
                'age' => 30,
                'bio' => 'Cast biography',
            ])
            ->assertRedirect(route('admin.casts.index'));
        $cast = Cast::where('name', 'Manageable Cast')->firstOrFail();

        $this->put(route('admin.casts.update', $cast), [
            'name' => 'Updated Manageable Cast',
            'age' => 31,
            'bio' => 'Updated biography',
        ])->assertRedirect(route('admin.casts.index'));
        $this->assertDatabaseHas('casts', ['id' => $cast->id, 'name' => 'Updated Manageable Cast']);

        $this->post(route('admin.actors.store'), [
            'cast_id' => $cast->id,
            'film_id' => $film->id,
            'actor' => 'Test Character',
        ])->assertRedirect(route('admin.casts.index'));
        $peran = Peran::where('actor', 'Test Character')->firstOrFail();

        $this->get(route('admin.casts.index', ['search' => 'Test Character']))
            ->assertOk()
            ->assertSee('Kelola Cast &amp; Aktor', false)
            ->assertSee('Test Character')
            ->assertSee('Updated Manageable Cast');

        $this->put(route('admin.actors.update', $peran), [
            'cast_id' => $cast->id,
            'film_id' => $film->id,
            'actor' => 'Updated Test Character',
        ])->assertRedirect(route('admin.casts.index'));

        $this->delete(route('admin.casts.destroy', $cast))
            ->assertRedirect(route('admin.casts.index'));
        $this->assertDatabaseMissing('casts', ['id' => $cast->id]);
        $this->assertDatabaseMissing('perans', ['id' => $peran->id]);
    }

    public function test_admin_can_view_and_delete_users_without_deleting_self(): void
    {
        $admin = $this->createUser('admin', 'admin-users-management@example.com');
        $user = $this->createUser('user', 'remove-user@example.com');
        $film = Film::query()->firstOrFail();
        $comment = Kritik::create([
            'comment' => 'Comment belonging to deleted user',
            'rating' => 0,
            'film_id' => $film->id,
            'user_id' => $user->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('remove-user@example.com');

        $this->delete(route('admin.users.destroy', $admin))
            ->assertSessionHasErrors('user');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);

        $this->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status', 'Pengguna dan komentarnya berhasil dihapus.');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('kritiks', ['id' => $comment->id]);
    }

    private function createUser(string $roleName, string $email): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);
        $profile = Profile::create([
            'umur' => 25,
            'bio' => 'Admin CRUD test account',
            'alamat' => 'Test address',
        ]);

        $user = new User([
            'name' => ucfirst($roleName),
            'email' => $email,
            'password' => 'password123',
        ]);
        $user->role = $roleName;
        $user->role_id = $role->id;
        $user->profile_id = $profile->id;
        $user->save();

        return $user;
    }
}
