<?php

use Illuminate\Database\Seeder;

class ProjectRoleSeed extends Seeder
{
    public function run()
    {
        $company = App\Models\Company::first();

        $boardItems = factory(App\Models\Role::class, 2)->make();

        $project = $company->projects()->first();

        $project->roles()->saveMany($boardItems);
    }
}
