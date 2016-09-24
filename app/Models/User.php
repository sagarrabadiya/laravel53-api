<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Models;

use App\Core\Exceptions\ValidationException;
use App\Core\Helpers\Defaults;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Watson\Validating\ValidatingTrait;

/**
 * Class User
 * @package App
 */
class User extends Authenticatable
{
    use SoftDeletes, ValidatingTrait, HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'company_id', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'company_id' => 'integer'
    ];

    /**
     * @var array
     */
    protected $fillable = ['username', 'designation', 'firstname', 'lastname', 'email', 'avatar', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'deleted_at',
        'company_id',
        'remember_token'
    ];

    protected $rules = [
        'username'  =>  'required|unique:users,username',
        'password'    =>  'required',
        'firstname' =>  'required',
        'lastname'  =>  'required',
        'email' =>  'required|email|unique:users,email',
        'avatar'    =>  'url'
    ];

    /**
     * @var bool
     */
    protected $throwValidationExceptions = true;

    /**
     * @throws ValidationException
     */
    public function throwValidationException()
    {
        throw new ValidationException($this->getErrors());
    }

    /**
     * @return bool
     */
    public function admin()
    {
        return $this->attributes['designation'] === 'admin';
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = app('hash')->make($value);
    }

    public function getNameAttribute()
    {
        return ucfirst($this->firstname)." ".ucfirst($this->lastname[0]).".";
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * @function provide relation with company
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * @function to provide relation with more than one projects
     * @return $this
     */
    public function projects()
    {
        $pivots = array_merge([
            'role_id',
            'settings'
        ], Defaults::permissionNames()->all());
        return $this->belongsToMany(Project::class, 'projects_teams', 'user_id', 'project_id')
            ->withPivot($pivots)->withTimestamps();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function project($id)
    {
        return $this->projects()->findOrFail($id);
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
