<?php

namespace Kambo\LLVM;

/**
 * Class LLVMTypeRef
 *
 * Lorem ipsum dolor
 *
 * @author  Bohuslav Simek <bohuslav@simek.si>
 * @license MIT
 */
class LLVMTypeRef
{
    private $ffiStructure;

    public static function marshal($ffiStructure)
    {
        $instance = new self;
        $instance->ffiStructure = $ffiStructure;

        return $instance;
    }

    public function demarshal()
    {
        return $this->ffiStructure;
    }

    function __destruct()
    {
        // TODO this starts to be interesting...
    }
}
