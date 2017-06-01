<?php

namespace Tooleks\Laravel\Presenter\Exceptions;

use Tooleks\Laravel\Presenter\Contracts\PresenterException as PresenterExceptionContract;

/**
 * Class AttributeNotFoundException.
 *
 * @package Tooleks\Laravel\Presenter\Exceptions
 * @author Oleksandr Tolochko <tooleks@gmail.com>
 */
class AttributeNotFoundException extends \RuntimeException implements PresenterExceptionContract
{

}
