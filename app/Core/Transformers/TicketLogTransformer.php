<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Transformers;

use Spatie\Activitylog\Models\Activity;

/**
 * Class TicketLogTransformer
 * @package App\Transformers
 */
class TicketLogTransformer extends Transformer
{

    protected $defaultIncludes = ['causer'];

    /**
     * @param Activity $log
     * @return array
     */
    public function transform(Activity $log)
    {
        return [
            'id'    =>  $log->id,
            'description'    =>  $log->description,
            'meta'      =>  $log->properties,
            'created_at'    =>  $log->created_at->toIso8601String(),
            'updated_at'    =>  $log->updated_at->toIso8601String()
        ];
    }

    /**
     * @param Activity $log
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeCauser(Activity $log)
    {
        if ($log->causer) {
            return $this->item($log->causer, new UserTransformer);
        }
        return null;
    }

    /**
     * @param TicketLog $log
     * @return \League\Fractal\Resource\Item|null
     */
    public function includeActionBy(TicketLog $log)
    {
        if ($log->actionBy) {
            return $this->item($log->actionBy, new UserTransformer);
        }
        return null;
    }
}
