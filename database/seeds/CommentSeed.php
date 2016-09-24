<?php

/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

use Illuminate\Database\Seeder;

class CommentSeed extends Seeder
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

        $project = $company->projects()->first();

        $ticket = $project->tickets()->first();
        $board_item = $project->boardItems()->first();

        if($ticket) {
            $ticket->comments()->saveMany(factory(\App\Models\Comment::class,2)->make(['created_by' => $user->id]));
        }

        if($board_item) {
            $board_item->comments()->saveMany(factory(\App\Models\Comment::class,2)->make(['created_by' => $user->id]));
        }
    }
}
