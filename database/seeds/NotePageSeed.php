<?php

use Illuminate\Database\Seeder;

class NotePageSeed extends Seeder
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

        $pages = factory(App\Models\NotePage::class, 2)->make([
            'created_by'    =>  $user->id,
            'updated_by'    =>  $user->id
        ]);

        $project = $company->projects()->first();

        $project->notes()->first()->pages()->saveMany($pages);
    }
}
