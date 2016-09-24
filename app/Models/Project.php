<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Models;

use App\Core\Helpers\Defaults;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Project
 * @package App
 */
class Project extends MainModel
{
    use SoftDeletes;

    /**
     * validation rules
     * @var array
     */
    protected $rules = [
        'name'  =>  'required',
        'settings.logo' =>  'url'
    ];

    /**
     * @var array
     */
    public $guarded = ['id', 'company_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * @var array
     */
    public $fillable = ['name', 'settings'];

    /**
     * @var array
     */
    protected $casts = [
        'active'    =>  'boolean',
        'settings' => 'json'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'company_id',
        'deleted_at'
    ];

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeArchive($query)
    {
        return $query->where('active', false);
    }

    /**
     * @function to provide relation with company
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * @function to provide relation with more than one roles
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        return $this->hasMany(Role::class, 'project_id');
    }

    /**
     * @function to provide relation with team members
     * @return $this
     */
    public function team()
    {
        $pivots = array_merge([
            'role_id',
            'settings'
        ], Defaults::permissionNames()->all());
        return $this->belongsToMany(User::class, 'projects_teams', 'project_id', 'user_id')
            ->withPivot($pivots)->withTimestamps();
    }

    /**
     * @function to provide relation with board items
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function boardItems()
    {
        return $this->hasMany(BoardItem::class, 'project_id');
    }

    /**
     * @function to provide relation with milestones
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function milestones()
    {
        return $this->hasMany(Milestone::class, 'project_id');
    }

    /**
     * @function to provide relation with note
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'project_id');
    }

    /**
     * @function to provide relation with tickets
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'project_id');
    }

    /**
     * @param Model $parent
     * @param array $attributes
     * @param string $table
     * @param bool $exists
     * @return ProjectTeamPivot
     */
    public function newPivot(Model $parent, array $attributes, $table, $exists)
    {
        return new ProjectTeamPivot($parent, $attributes, $table, $exists);
    }
}
