<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Models;

use App\Core\Models\TrackUser;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Milestone
 * @package App\Models
 */
class Milestone extends MainModel
{
    use SoftDeletes, TrackUser;

    /**
     * @var array
     */
    public $guarded = ['id'];

    /**
     * @var array
     */
    protected $fillable = [ 'title', 'description', 'deadline', 'type', 'status' ];

    /**
     * @var array
     */
    protected $dates = ['deadline'];

    /**
     * @var array
     */
    protected $hidden = [
        'deleted_at',
        'project_id',
        'responsible_member_id',
        'created_by',
        'updated_by'
    ];

    /**
     * @var array
     */
    protected $rules = [
        'title'  =>  'required',
        'description'    =>  'required',
        'deadline'  =>  'required|date_format:Y-m-d H:i:s'
    ];

    /**
     * @function to provide relation with project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * @function to provide relation with responsible member user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function responsibleMember()
    {
        return $this->belongsTo(User::class, 'responsible_member_id');
    }

    /**
     * @function to provide relation with tickets
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'milestone_id');
    }
}
