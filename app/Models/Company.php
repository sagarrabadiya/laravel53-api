<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Subscription;

/**
 * Class Company
 * @package App\Models
 */
class Company extends MainModel
{

    use SoftDeletes, Billable;

    /**
     * @var array
     */
    protected $casts = [
        'settings' => 'json'
    ];

    /**
     * @var array
     */
    protected $dates = ['trial_ends_at'];

    /**
     * @var array
     */
    public $guarded = ['id'];

    /**
     * @var array
     */
    protected $fillable = ['name', 'domain', 'settings'];


    protected $rules = [
        'name' => 'required',
        'domain' => 'required|unique:companies,domain',
        'settings'  =>  'array'
    ];

    /**
     * @param $query
     * @param $domain
     * @return mixed
     */
    public function scopeByDomain($query, $domain)
    {
        return $query->where('domain', $domain)->first();
    }

    /**
     * Get all of the subscriptions for the user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'company_id')->orderBy('created_at', 'desc');
    }

    /**
     * @function to provide relation with projects
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'company_id');
    }

    /**
     * @function to provide relation with users
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'company_id');
    }

    /**
     * @function to provide relation with file
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany(File::class, 'company_id');
    }
}
