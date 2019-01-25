<?php

namespace Kambo\LLVM\Assert;

use Assert\Assertion as BaseAssertion;

/**
 * Assertions and guard methods for input validation.
 * Subclassed for triggering custom exception.
 *
 * @author  Bohuslav Simek <bohuslav@simek.si>
 * @license BSD
 */
class Assertion extends BaseAssertion
{
    protected static $exceptionClass = InvalidArgumentException::class;
}
