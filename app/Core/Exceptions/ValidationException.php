<?php
/**
 * Author: sagar <sam.coolone70@gmail.com>
 *
 */

namespace App\Core\Exceptions;

use Illuminate\Contracts\Support\MessageBag;

class ValidationException extends \Exception
{


    /**
     * @var MessageBag
     */
    protected $bag;

    public function __construct(MessageBag $errorBag)
    {
        parent::__construct('given data failed to pass validation');
        $this->bag = $errorBag;
    }

    /**
     * @return MessageBag
     */
    public function getErrors()
    {
        return $this->bag;
    }
}
