<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Core\Transformers;

use App\Models\Project;

/**
 * Class ProjectTransformer
 * @package App\Transformers
 */
class ProjectTransformer extends Transformer
{
    /**
     * @param $project
     * @return array
     */
    public function transform(Project $project)
    {
        return [
            'id'    =>    (int) $project->id,
            'name'    =>    $project->name,
            'active' =>     $project->active,
            'settings'    =>    $project->settings,
            'created_at'    =>    $project->created_at->toIso8601String(),
            'updated_at'    =>    $project->updated_at->toIso8601String()
        ];
    }
}
