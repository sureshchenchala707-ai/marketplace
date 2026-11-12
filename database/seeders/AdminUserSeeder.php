<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = env('ADMIN_NAME');
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (!$name || !$email || !$password || strlen($password) < 16) {
            throw new RuntimeException(
                'Configure ADMIN_NAME, ADMIN_EMAIL and a password of at least 16 characters in .env.'
            );
        }

        DB::transaction(function () use ($name, $email, $password) {

            $adminRole = Role::where('slug', 'admin')
                ->where('status', true)
                ->firstOrFail();

            $user = User::where('email', $email)->first();

            if ($user) {
                if (!$user->roles()->where('slug', 'admin')->exists()) {
                    throw new RuntimeException(
                        'This email already belongs to another account. Admin promotion requires separate authorization.'
                    );
                }

                return;
            }

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);

            $user->roles()->attach($adminRole->id);
        });

        $this->command->info('Admin account created or already exists.');
    }
}
