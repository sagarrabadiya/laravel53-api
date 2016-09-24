<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Http\Controllers;

use App\Events\Ticket\TicketAssigneeChanged;
use App\Events\Ticket\TicketCreated;
use App\Events\Ticket\TicketStatusChanged;
use App\Events\Ticket\TicketUpdated;
use App\Models\Project;
use App\Models\Ticket;
use App\Core\Transformers\TicketTransformer;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;

/**
 * Class TicketsController
 * @package App\Http\Controllers
 */
class TicketsController extends Controller
{
    /**
     * @var TicketTransformer
     */
    protected $transformer;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * TicketsController constructor.
     * @param TicketTransformer $ticketTransformer
     * @param Dispatcher $dispatcher
     */
    public function __construct(TicketTransformer $ticketTransformer, Dispatcher $dispatcher)
    {
        $this->transformer = $ticketTransformer;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Request $request
     * @param \App\Models\Project $project
     * @return mixed
     */
    public function index(Request $request, Project $project)
    {
        $this->authorize('read', [new Ticket, $project]);
        $tickets = $project->tickets()
            ->with(['assignedBy', 'assignedTo', 'milestone', 'creator', 'updater'])
            ->assignedTo($request->input('assigned_to', null))
            ->status($request->input('status', null))
            ->paginate(self::PAGER);
        return response()->paginator($tickets, $this->transformer, ['key' => 'tickets']);
    }

    /**
     * @param \App\Models\Project $project
     * @param \App\Models\Ticket $ticket
     * @return mixed
     */
    public function show(Project $project, Ticket $ticket)
    {
        $this->authorize('read', [$ticket, $project]);
        return response()->item($ticket, $this->transformer, ['key' => 'ticket']);
    }

    /**
     * @param Request $request
     * @param \App\Models\Project $project
     * @return mixed
     */
    public function store(Request $request, Project $project)
    {
        $ticket = new Ticket($request->all());
        $this->authorize('write', [$ticket, $project]);
        $ticket->status = $request->input('status', 'new');
        $ticket->sequence_id = Ticket::getNextSequence($project->id);
        // validate and check if linked resources are valid
        $resources = $this->getLinkedResources($project, $request->only(['milestone_id', 'assigned_to', 'assigned_by']));
        $ticket->assignedBy()->associate(isset($resources['assigned_by']) ? $resources['assigned_by'] : $this->user());
        // save ticket and relate meta with ticket
        $project->tickets()->save($ticket);
        $this->dispatcher->fire(new TicketCreated($ticket));
        return response()->created($ticket, $this->transformer, ['key' => 'ticket']);
    }


    /**
     * @param Request $request
     * @param \App\Models\Project $project
     * @param \App\Models\Ticket $ticket
     * @return mixed
     */
    public function update(Request $request, Project $project, Ticket $ticket)
    {
        $this->authorize('write', [$ticket, $project]);
        $relatedResources = $this->getLinkedResources(
            $project,
            $request->only(['milestone_id', 'assigned_to', 'assigned_by'])
        );
        if (isset($relatedResources['milestone_id'])) {
            $ticket->milestone()->associate($relatedResources['milestone_id']);
        }
        if (isset($relatedResources['assigned_to'])) {
            $ticket->assignedTo()->associate($relatedResources['assigned_to']);
        }
        $ticket->assignedBy()->associate(
            isset($relatedResources['assigned_by']) ? $relatedResources['assigned_by'] : $this->user()
        );
        // update model
        $ticket->fill($request->all());
        $eventsToFire = $this->triggerUpdateEvent($ticket->getDirty());
        $ticket->save();
        foreach ($eventsToFire as $eventClass) {
            $this->dispatcher->fire(new $eventClass($ticket));
        }
        return response()->item($ticket, $this->transformer, ['key' => 'ticket']);
    }

    /**
     * @param \App\Models\Project $project
     * @param \App\Models\Ticket $ticket
     * @return mixed
     */
    public function destroy(Project $project, Ticket $ticket)
    {
        $this->authorize('write', [$ticket, $project]);
        $ticket->delete();
        return response()->deleted();
    }

    /**
     * make given linked resources validation
     * @param Project $project
     * @param array $resources
     * @return array
     */
    private function getLinkedResources(Project $project, $resources = array())
    {
        $resources = collect($resources)->filter(function ($value) {
            return !is_null($value);
        })->all();
        $result = array();
        foreach ($resources as $param => $value) {
            if ($param === 'milestone_id') {
                $result[$param] = $project->milestones()->findOrFail($value);
            } else {
                $result[$param] = $project->team->find($value);
                if (! $result[$param]) {
                    response()->error('use with id '. $value." not found!", 422);
                }
            }
        }
        return $result;
    }

    /**
     * returns the events to broadcast
     * @param array $dirtyAttributes
     * @return array
     */
    private function triggerUpdateEvent(array $dirtyAttributes = [])
    {
        $events = [];
        $attributes = collect($dirtyAttributes);
        if ($attributes->except(['assigned_to', 'status'])->count()) {
            $events [] = TicketUpdated::class;
        }
        if ($attributes->has('assigned_to')) {
            $events[] = TicketAssigneeChanged::class;
        }
        if ($attributes->has('status')) {
            $events [] = TicketStatusChanged::class;
        }
        return $events;
    }
}
