<?php

namespace Kambo\LLVM\Types;

use FFI\CData;

/**
 * Class LLVMExecutionEngineRef
 *
 * Lorem ipsum dolor
 *
 * @author  Bohuslav Simek <bohuslav@simek.si>
 * @license BSD
 */
class LLVMExecutionEngineRef extends BaseRef
{
    public function demarshal($ffi = null) : CData
    {
        // TODO prepare abstraction for this
        if ($this->ffiStructure === null) {
            $reflect            = new \ReflectionClass($this);
            $this->ffiStructure = $ffi->new($reflect->getShortName()." value");
        }

        return $this->ffiStructure;
    }
}
