<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Models;

use App\Core\Models\HasComments;
use App\Core\Models\HasFiles;
use App\Core\Models\TrackUser;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Ticket
 * @package App
 */
class Ticket extends MainModel
{
    use SoftDeletes, TrackUser, HasComments, HasFiles;

    /**
     * @var array
     */
    public $guarded = ['id'];

    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'resolution_type',
        'resolution_text',
        'milestone_id',
        'assigned_by',
        'assigned_to'
    ];

    /**
     * @var array
     */
    protected $rules = [
        'title'  =>  'required',
        'description'    =>  'required',
        'priority'  =>  'required'
    ];

    /**
     * function to calculate next sequence id
     * @param $projectId
     * @return int
     */
    public static function getNextSequence($projectId)
    {
        $builder = (new static)->newQuery();
        $maxId = $builder->where('project_id', $projectId)
            ->max('sequence_id');
        return is_null($maxId) ? 1 : $maxId + 1;
    }

    /**
     * @function to provide relation with project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * @function to provide relation with milestone
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function milestone()
    {
        return $this->belongsTo(Milestone::class, 'milestone_id');
    }

    /**
     * @function to provide relation with assignedBy
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * @function to provide relation with assigned_to
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * @param $query
     * @param $status
     * @return mixed
     */
    public function scopeStatus($query, $status)
    {
        return ! is_null($status) ? $query->where('status', $status) : $query;
    }

    /**
     * @param $query
     * @param $assigned_to
     * @return mixed
     */
    public function scopeAssignedTo($query, $assigned_to)
    {
        return ! is_null($assigned_to) ? $query->where('assigned_to', $assigned_to) : $query;
    }

    /**
     * @param $query
     * @param $sequence
     * @return mixed
     */
    public function ScopeBySequenceId($query, $sequence)
    {
        return $query->where('sequence_id', $sequence)->first();
    }
}
