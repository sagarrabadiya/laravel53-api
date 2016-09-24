<?php

use Illuminate\Database\Seeder;

class NoteSeed extends Seeder
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

        $notes = factory(App\Models\Note::class, 2)->make([
            'created_by'    =>  $user->id,
            'updated_by'    =>  $user->id
        ]);

        $project = $company->projects()->first();

        $project->notes()->saveMany($notes);
    }
}
