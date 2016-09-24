<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Models;

use App\Core\Models\TrackUser;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Note
 * @package App\Models
 */
class Note extends MainModel
{
    use SoftDeletes, TrackUser;

    /**
     * @var array
     */
    public $guarded = ['id'];

    /**
     * @var array
     */
    protected $fillable = ['title', 'description'];

    /**
     * @var array
     */
    protected $hidden = [
        'deleted_at',
        'project_id',
        'created_by',
        'updated_by'
    ];

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

    /**
     * @function to provide relation with pages
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pages()
    {
        return $this->hasMany(NotePage::class, 'note_id');
    }
}
