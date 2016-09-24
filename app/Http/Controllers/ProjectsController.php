<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Http\Controllers;

use App\Core\Helpers\Defaults;
use App\Core\Transformers\ProjectTransformer;
use App\Events\Team\UserInvited;
use App\Models\Project;
use Illuminate\Http\Request;

/**
 * Class ProjectsController
 * @package App\Http\Controllers
 */
class ProjectsController extends Controller
{
    /**
     * @var ProjectTransformer
     */
    private $transformer;

    /**
     * ProjectsController constructor.
     * @param ProjectTransformer $transformer
     */
    public function __construct(ProjectTransformer $transformer)
    {
        $this->middleware('project.create', ['only' =>  'store']);
        $this->middleware('project.update', ['only' =>  'update']);
        $this->transformer = $transformer;
    }


    /**
     * @return mixed
     */
    public function index()
    {
        $projects = $this->user()->projects;
        return response()->collection($projects, $this->transformer, ['key' => 'projects']);
    }


    /**
     * @param Project $project
     * @return mixed
     */
    public function show(Project $project)
    {
        return response()->item($project, $this->transformer);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->authorize('create', Project::class);
        $inputs = collect($request->all());
        $inputs = $inputs->merge(['active' => true]);
        $project = new Project($inputs->all());
        $project->settings = Defaults::mergeSettings(Defaults::projectSettings()->all(), $request->input('settings', []));
        $this->user()->company->projects()->save($project);
        // add current user to project as admin
        $project->team()->sync([$this->user()->id => Defaults::getAdminPermissions()], false);
        event(new UserInvited($this->user(), $project));
        return response()->created($project, $this->transformer, ['key' => 'project'], 201);
    }

    /**
     * @param Project $project
     * @param Request $request
     * @return mixed
     */
    public function update(Project $project, Request $request)
    {
        $this->authorize('update', $project);
        $inputs = $request->all();
        $inputs['settings'] = Defaults::mergeSettings(Defaults::projectSettings(), $request->input('settings', []));
        $project->update($request->all());
        return response()->item($project, $this->transformer, ['key' => 'project']);
    }

    /**
     * @param Project $project
     * @return mixed
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();
        return response()->deleted();
    }

    /**
     * @param Project $project
     * @return mixed
     */
    public function statistics(Project $project)
    {
        $response = [];
        $response['team'] = $project->team()->count();
        $response['board_items'] = $project->boardItems()->count();
        $response['milestones'] = $project->milestones()->count();
        $response['notes'] = $project->notes()->count();
        $response['tickets'] = [];
        $response['tickets']['total'] = $project->tickets()->count();
        $response['tickets']['closed'] = $project->tickets()->status('closed')->count();
        $response['tickets']['resolved'] = $project->tickets()->status('resolved')->count();
        $response['tickets']['new'] = $project->tickets()->status('new')->count();
        return response()->array($response);
    }
}
