<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CompanySeed::class);
        $this->call(ProjectSeed::class);
        $this->call(ProjectRoleSeed::class);
        $this->call(UserSeed::class);
        $this->call(BoardItemSeed::class);
        $this->call(MilestoneSeed::class);
        $this->call(NoteSeed::class);
        $this->call(NotePageSeed::class);
        $this->call(TicketSeed::class);
        $this->call(CommentSeed::class);
        $this->call(ActivitySeed::class);
        $this->call(ResourceLimitSeed::class);
    }
}
