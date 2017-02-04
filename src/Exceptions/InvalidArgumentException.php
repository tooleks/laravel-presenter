<?php

namespace Tooleks\Laravel\Presenter\Exceptions;

use Tooleks\Laravel\Presenter\Contracts\InvalidArgumentException as InvalidArgumentExceptionContract;

/**
 * Class InvalidArgumentException.
 *
 * @package Tooleks\Laravel\Presenter\Exceptions
 * @author Oleksandr Tolochko <tooleks@gmail.com>
 */
class InvalidArgumentException extends \InvalidArgumentException implements InvalidArgumentExceptionContract
{

}
