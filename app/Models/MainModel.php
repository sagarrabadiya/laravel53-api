<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Core\Exceptions\ValidationException;
use Watson\Validating\ValidatingTrait;

class MainModel extends Model
{

    use ValidatingTrait;

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
}
