<?php

use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
                                       'name' => Str::random(10),
                                       'email' => Str::random(10).'@user.com',
                                       'password' => bcrypt('123123'),
                                   ]);

        DB::table('users')->insert([
                                       'name' => Str::random(10),
                                       'email' => Str::random(10).'@admin.com',
                                       'password' => bcrypt('123123'),
                                       'is_admin' => 1,
                                   ]);
    }
}
