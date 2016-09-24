<?php

namespace App\Providers;

use App\Models\BoardItem;
use App\Models\Company;
use App\Models\Milestone;
use App\Models\Note;
use App\Models\NotePage;
use App\Models\Project;
use App\Models\Role;
use App\Models\Ticket;
use App\Models\User;
use App\Policies\BoardItemPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\MilestonePolicy;
use App\Policies\NotePolicy;
use App\Policies\RolesPolicy;
use App\Policies\TeamPolicy;
use App\Policies\TicketPolicy;
use App\Policies\UsersPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Company::class => CompanyPolicy::class,
        Project::class => TeamPolicy::class,
        Role::class => RolesPolicy::class,
        User::class => UsersPolicy::class,
        BoardItem::class => BoardItemPolicy::class,
        Milestone::class => MilestonePolicy::class,
        Note::class => NotePolicy::class,
        Ticket::class => TicketPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
    }
}
