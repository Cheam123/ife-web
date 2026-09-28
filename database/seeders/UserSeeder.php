<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Without events: the UserCreated listener would replace this password
        // with a random one and email it, locking the admin out of a fresh seed.
        User::withoutEvents(fn () => User::firstOrCreate(
            [
                'email'     => 'cheam@admin.com',
            ],
            [
                'name'      => 'Administrator',
                'username'  => 'U00001',
                'email'     => 'cheam@admin.com',
                'password'  => Hash::make('12345678'),
                'type'      => User::TYPE_ADMIN,
                'status'    => '1',
                'gender'    => 'M',
            ]
        ));
    }
}
