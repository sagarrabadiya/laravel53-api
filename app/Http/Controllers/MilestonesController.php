<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Http\Controllers;

use App\Events\Milestone\MilestoneCreated;
use App\Events\Milestone\MilestoneUpdated;
use App\Models\Milestone;
use App\Core\Transformers\MilestoneTransformer;
use App\Models\Project;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;

/**
 * Class MilestonesController
 * @package App\Http\Controllers
 */
class MilestonesController extends Controller
{
    /**
     * @var MilestoneTransformer
     */
    protected $transformer;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * MilestonesController constructor.
     * @param MilestoneTransformer $milestoneTransformer
     * @param Dispatcher $dispatcher
     */
    public function __construct(MilestoneTransformer $milestoneTransformer, Dispatcher $dispatcher)
    {
        $this->transformer = $milestoneTransformer;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param \App\Models\Project $project
     * @return mixed
     */
    public function index(Project $project)
    {
        $this->authorize('read', [new Milestone, $project]);
        $milestones = $project->milestones()->with(['creator', 'updater', 'responsibleMember'])->paginate(self::PAGER);
        return response()->paginator($milestones, $this->transformer, ['key' => 'milestones']);
    }

    /**
     * @param Request $request
     * @param \App\Models\Project $project
     * @return mixed
     */
    public function store(Request $request, Project $project)
    {
        $milestone = new Milestone($request->all());
        $this->authorize('write', [ $milestone, $project]);
        $milestone->status = 'created';

        // check if member is project member
        if ($member = $this->validateResponsibleMember($request, $project)) {
            $milestone->responsibleMember()->associate($member);
        } else {
            $milestone->responsibleMember()->dissociate();
        }

        $project->milestones()->save($milestone);
        $this->dispatcher->fire(new MilestoneCreated($milestone));
        return response()->created($milestone, $this->transformer, ['key' => 'milestone']);
    }

    /**
     * @param \App\Models\Project $project
     * @param \App\Models\Milestone $milestone
     * @return mixed
     */
    public function show(Project $project, Milestone $milestone)
    {
        $this->authorize('read', [ $milestone, $project ]);
        return response()->item($milestone, $this->transformer, ['key' => 'milestone']);
    }

    /**
     * @param Request $request
     * @param \App\Models\Project $project
     * @param \App\Models\Milestone $milestone
     * @return mixed
     */
    public function update(Request $request, Project $project, Milestone $milestone)
    {
        $this->authorize('write', [$milestone, $project]);

        if ($member = $this->validateResponsibleMember($request, $project)) {
            $milestone->responsibleMember()->associate($member);
        } else {
            $milestone->responsibleMember()->dissociate();
        }
        $milestone->update($request->all());
        $this->dispatcher->fire(new MilestoneUpdated($milestone));
        return response()->item($milestone, $this->transformer, ['key' => 'milestone']);
    }

    /**
     * @param \App\Models\Project $project
     * @param \App\Models\Milestone $milestone
     * @return mixed
     */
    public function destroy(Project $project, Milestone $milestone)
    {
        $this->authorize('write', [$milestone, $project]);
        $milestone->delete();
        return response()->deleted();
    }

    /**
     * @param \App\Models\Project $project
     * @param \App\Models\Milestone $milestone
     * @return mixed
     */
    public function statistics(Project $project, Milestone $milestone)
    {
        $tickets = [
            'total' => $milestone->tickets()->count(),
            'closed' => $milestone->tickets()->status('closed')->count()
        ];
        return response()->array(['tickets' => $tickets]);
    }

    /**
     * @param Request $request
     * @param \App\Models\Project $project
     * @return null| \App\Models\User
     */
    private function validateResponsibleMember(Request $request, Project $project)
    {
        $member = null;
        $memberId = $request->input('responsible_member_id', 0);
        if ($memberId) {
            $member = $project->team()->findOrFail($request->input('responsible_member_id'));
        } else {
            $member = null;
        }
        return $member;
    }
}
