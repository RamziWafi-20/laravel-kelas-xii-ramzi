<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\Genre;
use App\Models\Kritik;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FilmCommentTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_sees_login_link_and_direct_comment_request_returns_to_film_after_login(): void
    {
        $film = Film::query()->firstOrFail();

        $this->get(route('movies.show', $film))
            ->assertOk()
            ->assertSee('Login untuk berkomentar')
            ->assertDontSee(route('comments.store', $film));

        $this->post(route('comments.store', $film), ['comment' => 'Guest comment'])
            ->assertRedirect(route('login'));
        $this->get(route('login'))
            ->assertRedirect(route('home'))
            ->assertSessionHas('url.intended', route('movies.show', $film));

        $user = $this->createUser();
        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertRedirect(route('movies.show', $film));

        $this->assertDatabaseMissing('kritiks', [
            'film_id' => $film->id,
            'comment' => 'Guest comment',
        ]);
    }

    public function test_authenticated_user_comment_uses_the_logged_in_user_id(): void
    {
        $film = Film::query()->firstOrFail();
        $user = $this->createUser();

        $this->actingAs($user)
            ->post(route('comments.store', $film), [
                'comment' => 'A valid comment',
                'user_id' => $user->id + 100,
                'film_id' => $film->id + 100,
            ])
            ->assertRedirect(route('movies.show', $film))
            ->assertSessionHas('status', 'Komentar berhasil dikirim.');

        $this->assertDatabaseHas('kritiks', [
            'film_id' => $film->id,
            'user_id' => $user->id,
            'comment' => 'A valid comment',
        ]);
    }

    public function test_film_and_admin_pages_display_comment_author_and_time(): void
    {
        $film = Film::create([
            'title' => 'Comment Display Test Film',
            'sinopsis' => 'A film for testing comment displays',
            'year' => 2025,
            'poster' => 'storage/images/m1.jpg',
            'genre_id' => Genre::query()->value('id'),
        ]);
        $user = $this->createUser();
        $timestamp = now()->addDay()->startOfMinute();
        $comment = Kritik::create([
            'comment' => 'Visible author and date test',
            'rating' => 0,
            'film_id' => $film->id,
            'user_id' => $user->id,
        ]);
        $comment->created_at = $timestamp;
        $comment->save();

        $this->get(route('movies.show', $film))
            ->assertOk()
            ->assertSee('Comment User')
            ->assertSee($timestamp->diffForHumans());

        $admin = $this->createUser('admin', 'comment-admin@example.com');
        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Comment User')
            ->assertSee('Visible author and date test')
            ->assertSee($film->title)
            ->assertSee($timestamp->diffForHumans());
    }

    public function test_film_detail_shows_the_ten_most_recent_comments(): void
    {
        $film = Film::create([
            'title' => 'Recent Comments Test Film',
            'sinopsis' => 'A film for testing comment order',
            'year' => 2025,
            'poster' => 'storage/images/m1.jpg',
            'genre_id' => Genre::query()->value('id'),
        ]);
        $user = $this->createUser();

        foreach (range(1, 11) as $number) {
            $comment = Kritik::create([
                'comment' => 'Recent test comment ' . str_pad((string) $number, 2, '0', STR_PAD_LEFT),
                'rating' => 0,
                'film_id' => $film->id,
                'user_id' => $user->id,
            ]);
            $comment->created_at = now()->subDays(12 - $number);
            $comment->save();
        }

        $this->get(route('movies.show', $film))
            ->assertOk()
            ->assertSeeInOrder([
                'Recent test comment 11',
                'Recent test comment 10',
                'Recent test comment 02',
            ])
            ->assertDontSee('Recent test comment 01');
    }

    public function test_comment_must_be_present_and_no_longer_than_500_characters(): void
    {
        $film = Film::query()->firstOrFail();
        $user = $this->createUser();

        foreach (['', str_repeat('a', 501)] as $comment) {
            $response = $this->actingAs($user)
                ->from(route('movies.show', $film))
                ->post(route('comments.store', $film), ['comment' => $comment]);

            $response->assertRedirect(route('movies.show', $film))
                ->assertSessionHasErrors('comment');
        }

        $this->assertDatabaseMissing('kritiks', [
            'film_id' => $film->id,
            'user_id' => $user->id,
        ]);
    }

    private function createUser(string $roleName = 'user', string $email = 'comment-user@example.com'): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);
        $profile = Profile::create([
            'umur' => 20,
            'bio' => 'Test account',
            'alamat' => 'Test address',
        ]);

        $user = new User([
            'name' => $roleName === 'admin' ? 'Comment Admin' : 'Comment User',
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
