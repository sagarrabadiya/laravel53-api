<?php

use Illuminate\Database\Seeder;

class BoardItemSeed extends Seeder
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

        $boardItems = factory(App\Models\BoardItem::class, 2)->make([
            'created_by'    =>  $user->id,
            'updated_by'    =>  $user->id
        ]);

        $project = $company->projects()->first();

        $project->boardItems()->saveMany($boardItems);
    }
}
