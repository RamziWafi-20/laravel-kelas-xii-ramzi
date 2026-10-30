<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use App\Models\{User, Profile, Role};

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $admin = User::firstOrNew(['email' => 'admin@example.com']);

        if (! $admin->exists || ! $admin->profile_id) {
            $admin->profile_id = Profile::create([
                'alamat' => Faker::create('id_ID')->address,
                'umur' => 30,
                'bio' => 'administrator',
            ])->id;
        }

        if (! $admin->exists) {
            $admin->password = Hash::make('12345678');
            $admin->email_verified_at = now();
            $admin->remember_token = Str::random(10);
        }

        $admin->name = 'admin';
        $admin->role = 'admin';
        $admin->role_id = $role->id;
        $admin->save();
    }
}
