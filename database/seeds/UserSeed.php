<?php

use Illuminate\Database\Seeder;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = App\Models\Company::first();

        $users = factory(App\Models\User::class, 2)->make();
        $users[0]->email = 'a@b.com';
        $users[0]->designation = 'admin';
        // add users to company
        $company->users()->saveMany($users);

        // add users to project
        $projects = $company->projects()->get();
        $projects[0]->team()->saveMany($users);
    }
}
