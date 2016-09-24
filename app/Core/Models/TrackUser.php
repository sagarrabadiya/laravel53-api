<?php

/**
 * trait to add support for creator and updater of the resource
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Core\Models;

use App\Models\User;
use Auth;

trait TrackUser
{

    /**
     * @return bool
     */
    protected function hasCreator()
    {
        if (property_exists($this, 'trackUsers')) {
            return isset($this->trackUsers['creator'])
                ? $this->trackUsers['creator']
                : true;
        }
        return true;
    }

    /**
     * @return bool
     */
    protected function hasUpdater()
    {
        if (property_exists($this, 'trackUsers')) {
            return isset($this->trackUsers['updater'])
                ? $this->trackUsers['updater']
                : true;
        }
        return true;
    }

    /**
     * attach current user id as creator/updater when saving
     * @param array $options
     * @return mixed
     */
    public function save(array $options = [])
    {
        $created = property_exists($this, 'created_by')
            ? $this->created_by
            : 'created_by';

        $updated = property_exists($this, 'updated_by')
            ? $this->updated_by
            : 'updated_by';
        if (Auth::check()) {
            if (!$this->exists) {
                // if model not exists in database so its created and attach created_by
                if ($this->hasCreator()) {
                    $this->setAttribute($created, Auth::user()->id);
                }
            } else {
                // if model exists then its update so attach updated_by
                if ($this->hasUpdater()) {
                    $this->setAttribute($updated, Auth::user()->id);
                }
            }
        }
        return parent::save($options);
    }

    /**
     * attach current user id as creator when creating
     * @param array $attributes
     * @return mixed
     */
    public static function create(array $attributes = [])
    {
        $model = new static;
        $created = property_exists($model, 'created_by')
            ? $model->created_by
            : 'created_by';

        if (Auth::check() && $model->hasCreator()) {
            $attributes[$created] = Auth::user()->id;
        }

        return parent::create($attributes);
    }

    /**
     * attach current user id as updater when updating
     * @param array $attributes
     * @param array $options
     * @return mixed
     */
    public function update(array $attributes = [], array $options = [])
    {
        $updated = property_exists($this, 'updated_by')
            ? $this->updated_by
            : 'updated_by';
        if (Auth::check() && $this->hasUpdater()) {
            $this->fill($attributes);
            if ($this->isDirty(array_keys($attributes))) {
                $attributes[$updated] = Auth::user()->id;
            }
        }
        return parent::update($attributes, $options);
    }

    /**
     * @function to provide relation with created by user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        $created = property_exists($this, 'created_by')
            ? $this->created_by
            : 'created_by';
        if ($created && $this->hasCreator()) {
            return $this->belongsTo(User::class, $created);
        } else {
            return null;
        }
    }

    /**
     * @function to provide relation with updated by user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater()
    {
        $updated = property_exists($this, 'updated_by')
            ? $this->updated_by
            : 'updated_by';
        if ($updated && $this->hasUpdater()) {
            return $this->belongsTo(User::class, $updated);
        } else {
            return null;
        }
    }
}
