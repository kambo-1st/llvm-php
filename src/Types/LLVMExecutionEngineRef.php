<?php

namespace Kambo\LLVM\Types;

/**
 * Class LLVMExecutionEngineRef
 *
 * Lorem ipsum dolor
 *
 * @author  Bohuslav Simek <bohuslav@simek.si>
 * @license MIT
 */
class LLVMExecutionEngineRef
{
    private $ffiStructure;

    public function __construct()
    {

    }

    public static function marshal($ffiStructure)
    {
        $instance = new self;
        $instance->ffiStructure = $ffiStructure;

        return $instance;
    }

    public function demarshal($ffi=null)
    {
        // TODO prepare abstraction for this
        if ($this->ffiStructure === null) {
            $reflect            = new \ReflectionClass($this);
            $this->ffiStructure = $ffi->new($reflect->getShortName()." value");
        }

        return $this->ffiStructure;
    }

    function __destruct()
    {
        // TODO this starts to be interesting...
    }
}
