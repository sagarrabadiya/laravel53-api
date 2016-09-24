<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Http\Controllers;

use App\Core\Exceptions\ValidationException;
use App\Core\Helpers\Defaults;
use App\Core\Transformers\TeamTransformer;
use App\Core\Transformers\UserTransformer;
use App\Events\Team\UserInvited;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

/**
 * Class TeamsController
 * @package App\Http\Controllers
 */
class TeamsController extends Controller
{


    /**
     * @param $project
     * @return mixed
     */
    public function index(Project $project)
    {
        $team = $project->team;
        return response()->collection($team, new UserTransformer, ['key' => 'team']);
    }

    /**
     * @param Request $request
     * @param $project
     * @return mixed
     */
    public function store(Request $request, Project $project)
    {
        $this->validate($request, $this->rules($request));
        $this->authorize('write-member', $project);

        list($user, $pivot) = $this->getPivotData($request, $project);
        $pivot['settings'] = json_encode($pivot['settings']);
        $project->team()->sync([$user->id => $pivot], false);
        $user = $project->team()->find($user->id);
        event(new UserInvited($user, $project));
        return response()->created($user, new TeamTransformer, ['key' => 'user']);
    }


    /**
     * @param $project
     * @param $member
     * @return mixed
     */
    public function show(Project $project, $member)
    {
        $this->authorize('read-member', $project);
        $teamMember = $project->team()->findOrFail($member);
        return response()->item($teamMember, new TeamTransformer, ['key' => 'user']);
    }

    /**
     * @param Request $request
     * @param \App\Models\Project $project
     * @param $userId
     * @return mixed
     */
    public function update(Request $request, Project $project, $userId)
    {
        $this->validate($request, $this->rules($request));
        $this->authorize('write-member', $project);
        $teamMember = $project->team()->findOrFail($userId);
        list($user, $pivot) = $this->getPivotData($request, $project, $userId);
        $teamMember->pivot->update($pivot);
        return response()->item($teamMember, new TeamTransformer, ['key' => 'user']);
    }

    /**
     * @param $project
     * @param $userId
     * @return mixed
     */
    public function destroy(Project $project, $userId)
    {
        $this->authorize('write-member', $project);
        $project->team()->detach($userId);
        return response()->deleted();
    }

    /**
     * @param Request $request
     * @param $project
     * @param null $userId
     * @return array
     * @throws ValidationException
     */
    protected function getPivotData(Request $request, Project $project, $userId = null)
    {
        $user = $this->user()->company->users()->find($request->has('user_id') ? $request->input('user_id') : $userId);
        if (!$user) {
            throw new ValidationException(new MessageBag(['username is not in team']));
        }
        if ($request->has('role_id')) {
            $pivot = $project->roles()->findOrFail($request->input('role_id'))->permissions()->all();
            $pivot['role_id'] = $request->input('role_id');
        } else {
            $pivot = $request->except(['user_id']);
        }
        $pivot['settings'] = Defaults::mergeSettings(Defaults::memberDefaultSettings(), $request->input('settings', []));

        return array($user, $pivot);
    }

    /**
     * returns validation rules
     * @param Request $request
     * @return array
     */
    protected function rules(Request $request)
    {
        $rules = ['user_id' => 'required|exists:users,id'];
        $permissions = Defaults::permissionNames();
        foreach ($permissions as $p) {
            $rules[$p] = 'required_without:role_id';
        }
        $rules['role_id'] = 'required_without_all:'.$permissions->implode(',');
        if ($request->isMethod('post')) {
            return $rules;
        }
        unset($rules['user_id']);

        return $rules;
    }

    /**
     * throws validationException
     * @param Request $request
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws ValidationException
     */
    protected function throwValidationException(Request $request, $validator)
    {
        throw new ValidationException($validator->getMessageBag());
    }
}
