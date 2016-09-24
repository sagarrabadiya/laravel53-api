<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Transformers;

use App\Models\Activity;
use App\Models\BoardItem;
use App\Models\Comment;
use App\Models\Ticket;

class ActivityTransformer extends Transformer
{
    protected $defaultIncludes = ['creator', 'ticket', 'boardItem', 'comment'];

    public function transform(Activity $activity)
    {
        return [
            'meta'    =>    $activity->meta,
            'activity_type'    =>    $activity->activity_type,
            'created_at'    =>    $activity->created_at->toIso8601String(),
            'updated_at'    =>    $activity->updated_at->toIso8601String()
        ];
    }

    public function includeBoardItem(Activity $activity)
    {
        if ($activity->parent instanceof BoardItem) {
            $transformer = new BoardItemTransformer();
            $transformer->setDefaultIncludes([]);
            return $this->item($activity->parent, $transformer);
        }
        return null;
    }

    public function includeTicket(Activity $activity)
    {
        if ($activity->parent instanceof Ticket) {
            $transformer = new TicketTransformer;
            $transformer->setDefaultIncludes([]);
            return $this->item($activity->parent, $transformer);
        }
        return null;
    }

    public function includeComment(Activity $activity)
    {
        if ($activity->parent instanceof Comment) {
            $transformer = new CommentTransformer();
            $transformer->setDefaultIncludes([]);
            return $this->item($activity->parent, $transformer);
        }
        return null;
    }

    public function includeCreator(Activity $activity)
    {
        if ($activity->creator) {
            return $this->item($activity->creator, new UserTransformer);
        }
        return null;
    }
}
