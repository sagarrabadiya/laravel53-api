<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Models;

use App\Core\Models\TrackUser;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class File
 * @package App\Models
 */
class File extends MainModel
{
    use SoftDeletes, TrackUser;

    /**
     * @var array
     */
    public $guarded = ['id'];

    /**
     * @var array
     */
    protected $fillable = ['name', 'salt', 'ext', 'size'];

    /**
     * @var array
     */
    protected $casts = [
        'is_orphan' => 'boolean'
    ];

    protected $trackUsers = [
        'updater' => false
    ];

    /**
     * @var array
     */
    protected $rules = [];
    
    /**
     * @var array
     */
    public $hidden = ['deleted_at','is_orphan','created_by'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function parent()
    {
        return $this->morphTo();
    }
}
