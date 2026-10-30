<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_link_targets_the_local_modal_without_loading_a_remote_page(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="#myModal" data-toggle="modal" data-target="#myModal"', false)
            ->assertSee('Create an account');
    }

    public function test_registration_creates_a_hashed_user_and_logs_them_in(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'New Member',
            'email' => 'new-member@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'new-member@example.com')->firstOrFail();

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame('user', $user->role);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertNotSame('password123', $user->password);
    }

    public function test_registration_validates_email_password_and_confirmation(): void
    {
        $response = $this->from(route('home'))->post(route('register'), [
            'name' => 'New Member',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'different',
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHasErrors(['email', 'password']);
        $response->assertSessionHas('auth_form', 'register');
    }

    public function test_registration_validation_feedback_is_rendered_in_the_modal(): void
    {
        $response = $this->followingRedirects()
            ->from(route('home'))
            ->post(route('register'), [
                'name' => 'New Member',
                'email' => 'not-an-email',
                'password' => 'short',
                'password_confirmation' => 'different',
            ]);

        $response->assertOk()
            ->assertSee('Masukkan alamat email yang valid.')
            ->assertSee('Password minimal 8 karakter.')
            ->assertSee('modal(\'show\')', false);
    }

    public function test_login_redirects_users_and_admins_to_the_home_page(): void
    {
        $user = $this->createUser('user', 'member@example.com');
        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);

        auth()->logout();
        $admin = $this->createUser('admin', 'admin-login-flow@example.com');
        $this->post(route('login.store'), [
            'email' => $admin->email,
            'password' => 'password123',
        ])->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_sees_dashboard_link_on_home_but_regular_users_do_not(): void
    {
        $user = $this->createUser('user', 'member-dashboard-link@example.com');
        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertDontSee('href="' . route('admin.dashboard') . '"', false);

        auth()->logout();
        $admin = $this->createUser('admin', 'admin-dashboard-link@example.com');
        $this->actingAs($admin)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('href="' . route('admin.dashboard') . '"', false)
            ->assertSee('Dashboard');
    }

    public function test_login_uses_a_generic_error_for_invalid_credentials(): void
    {
        $this->createUser('user', 'member@example.com');

        $response = $this->from(route('home'))->post(route('login.store'), [
            'email' => 'member@example.com',
            'password' => 'incorrect',
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHasErrors(['credentials' => 'Email atau password salah.']);
        $this->assertGuest();
    }

    public function test_invalid_login_feedback_is_rendered_in_the_modal(): void
    {
        $this->createUser('user', 'member@example.com');

        $response = $this->followingRedirects()
            ->from(route('home'))
            ->post(route('login.store'), [
                'email' => 'member@example.com',
                'password' => 'incorrect',
            ]);

        $response->assertOk()
            ->assertSee('Email atau password salah.')
            ->assertSee('modal(\'show\')', false);
    }

    public function test_admin_dashboard_requires_an_admin_role(): void
    {
        $user = $this->createUser('user', 'member@example.com');
        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();

        $admin = $this->createUser('admin', 'admin-access-check@example.com');
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    }

    public function test_logout_invalidates_the_authenticated_session(): void
    {
        $user = $this->createUser('user', 'member@example.com');

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('home'))
            ->assertSessionHas('status', 'Anda berhasil logout.');

        $this->assertGuest();
    }

    private function createUser(string $roleName, string $email): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);
        $profile = Profile::create([
            'umur' => 20,
            'bio' => 'Test account',
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
