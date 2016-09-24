<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Role;
use App\Core\Transformers\RoleTransformer;
use Illuminate\Http\Request;

/**
 * Class ProjectRolesController
 * @package App\Http\Controllers
 */
class RolesController extends Controller
{
    /**
     * @var RoleTransformer
     */
    protected $transformer;

    /**
     * RolesController constructor.
     * @param RoleTransformer $roleTransformer
     */
    public function __construct(RoleTransformer $roleTransformer)
    {
        $this->transformer = $roleTransformer;
    }


    /**
     * @param $project
     * @return mixed
     */
    public function index(Project $project)
    {
        $roles = $project->roles()->get();
        return response()->collection(
            $roles,
            $this->transformer,
            ['key' => 'roles']
        );
    }

    /**
     * @param Request $request
     * @param $project
     * @return mixed
     */
    public function store(Request $request, Project $project)
    {
        $this->authorize('create', Role::class);
        $role = new Role($request->all());
        $project->roles()->save($role);
        return response()->created($role, $this->transformer, ['key' => 'role']);
    }

    /**
     * @param $project
     * @param $role
     * @return mixed
     */
    public function show(Project $project, Role $role)
    {
        return response()->item($role, $this->transformer, ['key' => 'role']);
    }

    /**
     * @param Request $request
     * @param $project
     * @param $role
     * @return mixed
     */
    public function update(Request $request, Project $project, Role $role)
    {
        $this->authorize('update', $role);
        $role->update($request->all());
        //update existing team members permission with the role id
        if (count($request->except('name'))) {
            $project->team()->newPivotStatement()
                ->where('projects_teams.role_id', $role->id)
                ->update($request->except('name'));
        }
        return response()->item($role, $this->transformer, ['key' => 'role']);
    }

    /**
     * @param $project
     * @param $role
     * @return mixed
     */
    public function destroy(Project $project, Role $role)
    {
        $this->authorize('delete', $role);
        $role->delete();
        return response()->deleted();
    }
}
