<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Models;

use App\Core\Helpers\Defaults;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProjectRole
 * @package App
 */
class Role extends MainModel
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = "project_roles";

    /**
     * @var array
     */
    protected $hidden = ['deleted_at', 'project_id'];

    /**
     * @var array
     */
    public $guarded = ['id', 'project_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * @var array
     */
    protected $rules = [
        'name'  =>  'required'
    ];

    /**
     * assign validation parameters when created
     * Role constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        Defaults::permissionNames()->each(function ($permission) {
            $this->rules[$permission] = 'required';
            $this->fillable[] = $permission;
        });
        parent::__construct($attributes);
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
     * @function to filter role with project
     * @param $query
     * @param $project
     * @return mixed
     */
    public function scopeWithProject($query, $project)
    {
        return $query->where('project_id', $project);
    }

    /**
     * @return static
     */
    public function permissions()
    {
        return collect($this->toArray())->except(['name', 'created_at', 'updated_at', 'id', 'project_id']);
    }
}
