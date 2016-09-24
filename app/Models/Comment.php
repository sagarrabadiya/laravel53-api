<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Models;

use App\Core\Models\HasFiles;
use App\Core\Models\TrackUser;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Comment
 * @package App\Models
 */
class Comment extends MainModel
{
    use SoftDeletes, TrackUser, HasFiles;

    /**
     * @var array
     */
    public $guarded = ['id'];

    /**
     * @var array
     */
    protected $fillable = ['text'];

    /**
     * @var array
     */
    protected $with = ['creator', 'files'];

    /**
     * @var array
     */
    protected $rules = [
        'text' => 'required'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function parent()
    {
        return $this->morphTo();
    }
}
