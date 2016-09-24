<?php

use Illuminate\Database\Seeder;

class ProjectSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = App\Models\Company::first();
        $company->projects()->saveMany(factory(App\Models\Project::class, 2)->make());
    }
}
