<?php

use Illuminate\Database\Seeder;
use App\Company;

class MilestoneSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = App\Models\Company::first();

        $user = $company->users()->first();

        $milestones = factory(App\Models\Milestone::class, 2)->make([
            'created_by'    =>  $user->id,
            'updated_by'    =>  $user->id
        ]);

        $project = $company->projects()->first();

        $project->milestones()->saveMany($milestones);
    }
}
