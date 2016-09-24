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
use Watson\Validating\ValidatingTrait;

/**
 * Class BoardItem
 * @package App\Models
 */
class BoardItem extends MainModel
{
    use SoftDeletes, TrackUser, HasComments, HasFiles;

    /**
     * @var array
     */
    public $guarded = ['id'];

    /**
     * @var array
     */
    public $fillable = ['title', 'description'];

    /**
     * @var array
     */
    protected $hidden = ['deleted_at','project_id','created_by','updated_by'];

    /**
     * @var array
     */
    protected $rules = [
        'title'  =>  'required',
        'description'    =>  'required'
    ];


    /**
     * @function to provide relation with project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
