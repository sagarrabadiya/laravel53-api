<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Core\Models;

use App\Models\File;

trait HasFiles
{

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function files()
    {
        return $this->morphMany(File::class, 'parent');
    }
}
