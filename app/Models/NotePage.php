<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Models;

use App\Core\Models\TrackUser;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class NotePage
 * @package App\Models
 */
class NotePage extends MainModel
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
        'note_id',
        'created_by',
        'updated_by',
        'deleted_at'
    ];

    /**
     * @var array
     */
    protected $rules = [
        'title'  =>  'required',
        'description'    =>  'required'
    ];

    /**
     * @function provide relation with note
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function note()
    {
        return $this->belongsTo(Note::class, 'note_id');
    }
}
