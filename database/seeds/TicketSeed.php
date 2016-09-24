<?php

use Illuminate\Database\Seeder;

class TicketSeed extends Seeder
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

        $tickets = factory(App\Models\Ticket::class, 2)->make([
            'created_by'    =>  $user->id,
            'updated_by'    =>  $user->id
        ]);

        $project = $company->projects()->first();

        $project->tickets()->saveMany($tickets);
    }
}
