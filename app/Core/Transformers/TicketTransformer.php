<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Core\Transformers;

use App\Models\Ticket;

class TicketTransformer extends Transformer
{
    protected $defaultIncludes = ['assignedBy', 'assignedTo', 'creator', 'updater', 'milestone'];

    /**
     * @param Ticket $ticket
     * @return array
     */
    public function transform(Ticket $ticket)
    {
        $output = [
            'id'    =>    $ticket->id,
            'title'    =>    $ticket->title,
            'description'    =>    $ticket->description,
            'priority'    =>    $ticket->priority,
            'status'    =>    $ticket->status,
            'resolution_type' => $ticket->resolution_type,
            'resolution_text' => $ticket->resolution_text,
            'sequence' => $ticket->sequence_id,
            'created_at'    =>    $ticket->created_at->toIso8601String(),
            'updated_at'    =>    $ticket->updated_at->toIso8601String()
        ];
        if (!count($this->defaultIncludes)) {
            $output['assigned_to'] = $ticket->assigned_to;
            $output['assigned_by'] = $ticket->assigned_by;
            $output['created_by'] = $ticket->created_by;
            $output['updated_by'] = $ticket->updated_by;
        }
        return $output;
    }

    public function includeAssignedBy(Ticket $ticket)
    {
        if ($ticket->assignedBy) {
            return $this->item($ticket->assignedBy, new UserTransformer);
        }
        return null;
    }

    public function includeAssignedTo(Ticket $ticket)
    {
        if ($ticket->assignedTo) {
            return $this->item($ticket->assignedTo, new UserTransformer);
        }
        return null;
    }

    public function includeCreator(Ticket $ticket)
    {
        if ($ticket->creator) {
            return $this->item($ticket->creator, new UserTransformer);
        }
        return null;
    }

    public function includeUpdater(Ticket $ticket)
    {
        if ($ticket->updater) {
            return $this->item($ticket->updater, new UserTransformer);
        }
        return null;
    }

    public function includeMilestone(Ticket $ticket)
    {
        if ($ticket->milestone) {
            $transformer = new MilestoneTransformer;
            $transformer->setDefaultIncludes([]);
            return $this->item($ticket->milestone, $transformer);
        }
        return null;
    }
}
