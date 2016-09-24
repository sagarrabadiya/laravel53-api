<?php

namespace App\Providers;

use App\Models\User;
use Auth;
use Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
        $this->bindModels();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapWebRoutes();

        $this->mapApiRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => ['api'],
            'namespace' => $this->namespace,
            'prefix' => 'api',
        ], function ($router) {
            require base_path('routes/api.php');
        });
    }

    /**
     * bind models to router
     */
    protected function bindModels()
    {
        // company binding
        Route::bind('company', function ($id) {
            if (request()->user()->company_id != $id) {
                throw new AccessDeniedHttpException('access denied');
            }
            return request()->user()->company;
        });

        // project binding
        Route::bind('project', function ($id) {
            return request()->user()->project($id);
        });

        // role binding
        Route::bind('role', function ($id) {
            $project = request()->route('project');
            return $project->roles()->find($id);
        });

        //user binding
        Route::bind('user', function ($id) {
            return User::where('company_id', request()->user()->company_id)->find($id);
        });

        // board item binding
        Route::bind('board_item', function ($id) {
            $project = request()->route('project');
            return $project->boardItems()->find($id);
        });

        // milestone binding
        Route::bind('milestone', function ($id) {
            $project = request()->route('project');
            return $project->milestones()->find($id);
        });

        // note binding
        Route::bind('note', function ($id) {
            $project = request()->route('project');
            return $project->notes()->find($id);
        });

        // note page binding
        Route::bind('page', function ($id) {
            $note = request()->route('note');
            return $note->pages()->find($id);
        });

        // ticket binding
        Route::bind('ticket', function ($id) {
            $project = request()->route('project');
            return $project->tickets()->bySequenceId($id);
        });

        // comment binding
        Route::bind('comment', function ($id) {
            $parent = request()->route('ticket') ?: request()->route('board_item');
            return $parent->comments()->find($id);
        });
    }
}
