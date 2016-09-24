<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResourceLimit extends Model
{
    protected $guarded = ['id'];

    protected $table = 'resource_limits';

    public $timestamps = false;

    protected $casts = [
        'projects_allowed'  =>  'integer',
        'archived_projects_allowed' =>  'integer',
        'users_allowed' =>  'integer'
    ];


    /**
     * @param $query
     * @param $planId
     * @return mixed
     */
    public function scopePlan($query, $planId)
    {
        return $query->where('stripe_plan_identifier', $planId)->first();
    }
}
