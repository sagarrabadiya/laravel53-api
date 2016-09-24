<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Core\Transformers;

use App\Models\Milestone;

/**
 * Class MilestoneTransformer
 * @package App\Transformers
 */
class MilestoneTransformer extends Transformer
{

    protected $defaultIncludes = ['creator','responsibleMember', 'updater'];

    /**
     * @param $milestone
     * @return array
     */
    public function transform(Milestone $milestone)
    {
        return [
            'id'    =>    $milestone->id,
            'title'    =>    $milestone->title,
            'description'    =>    $milestone->description,
            'deadline'    =>    $milestone->deadline->toIso8601String(),
            'status'    =>    $milestone->status,
            'type'    =>    $milestone->type,
            'created_at'    =>    $milestone->created_at->toIso8601String(),
            'updated_at'    =>    $milestone->updated_at->toIso8601String()
        ];
    }

    public function includeCreator(Milestone $milestone)
    {
        if ($milestone->creator) {
            return $this->item($milestone->creator, new UserTransformer);
        }
        return null;
    }

    public function includeUpdater(Milestone $milestone)
    {
        if ($milestone->updater) {
            return $this->item($milestone->updater, new UserTransformer);
        }
        return null;
    }

    public function includeResponsibleMember(Milestone $milestone)
    {
        if ($milestone->responsibleMember) {
            return $this->item($milestone->responsibleMember, new UserTransformer);
        }
        return null;
    }
}
