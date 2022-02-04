<?php

use App\User;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker) {
        DB::table('users')->insert(
            [
                [
                    'name'              => 'テスト太郎',
                    'email'             => 'test@example.com', // 実在しないアドレス
                    'email_verified_at' => now(),
                    'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                    'remember_token'    => Str::random(10),
                    'postal_code'       => $faker->postcode,
                    'address'           => $faker->streetAddress,
                    'phone'             => $faker->phoneNumber
                ],
            ]
        );
        factory(User::class, 5)->create();
    }
}
