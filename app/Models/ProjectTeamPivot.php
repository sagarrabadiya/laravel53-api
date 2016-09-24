<?php
/**
 * Created by PhpStorm.
 * User: sagar
 * Date: 29/05/16
 * Time: 10:53 AM
 */

namespace App\Models;

use App\Core\Helpers\Defaults;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectTeamPivot extends Pivot
{

    /**
     * @var array
     */
    protected $fillable = ['settings', 'role_id'];

    protected $casts = [
        'settings' => 'json'
    ];

    /**
     * @var array
     */
    protected $hidden = ['project_id', 'user_id', 'role_id', 'created_at', 'updated_at', 'settings'];

    /**
     * assign casts attribute dynamically
     * @param Model $parent
     * @param array $attributes
     * @param string $table
     * @param bool $exists
     */
    public function __construct(Model $parent, array $attributes, $table, $exists)
    {
        parent::__construct($parent, $attributes, $table, $exists);
        $permissions = Defaults::permissionNames();
        $permissions->each(function ($p) {
            $this->casts[$p] = 'boolean';
            $this->fillable[] = $p;
        });
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'projects_teams';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}
