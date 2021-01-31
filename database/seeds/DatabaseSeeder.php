<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Setting;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->truncate();
        $user           = new User;
        $user->email    = "admin@example";
        $user->password = bcrypt('123456');
        $user->name     = "Admin";
        $user->active   = 1;
        $user->role_id  = 1;
        $user->save();

        \DB::table('settings')->truncate();
        $sett  = new Setting;
        $sett->system_name = "QuizApp";
        $sett->save();
    }
}
