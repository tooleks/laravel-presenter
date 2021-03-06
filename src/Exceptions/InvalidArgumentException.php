<?php

namespace Tooleks\Laravel\Presenter\Exceptions;

use Tooleks\Laravel\Presenter\Contracts\PresenterException as PresenterExceptionContract;

/**
 * Class InvalidArgumentException.
 *
 * @package Tooleks\Laravel\Presenter\Exceptions
 * @author Oleksandr Tolochko <tooleks@gmail.com>
 */
class InvalidArgumentException extends \InvalidArgumentException implements PresenterExceptionContract
{

}
