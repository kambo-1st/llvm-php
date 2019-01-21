<?php

namespace Kambo\LLVM\Types;

use FFI\CData;

/**
 * Common parent for all reference types
 *
 * LLVM uses a polymorphic type hierarchy which C cannot represent, therefore
 * parameters must be passed as base types. Despite the declared types, most
 * of the functions provided operate only on branches of the type hierarchy.
 * The declared parameter names are descriptive and specify which type is
 * required. Additionally, each type hierarchy is documented along with the
 * functions that operate upon it. For more detail, refer to LLVM's C++ code.
 *
 * @author  Bohuslav Simek <bohuslav@simek.si>
 * @license BSD
 */
class BaseRef implements Marshable
{
    protected $ffiStructure;

    /**
     * Marshal data into php format
     *
     * @param FFI\CData $ffiStructure LLVM wrapper
     *
     * @return BaseRef
     */
    public static function marshal(CData $ffiStructure)
    {
        $instance = new static;
        $instance->ffiStructure = $ffiStructure;

        return $instance;
    }

    /**
     * Demarshal data into php FFI format
     *
     * @return FFI\CData
     */
    public function demarshal() : CData
    {
        return $this->ffiStructure;
    }

    public function __destruct()
    {
        // TODO this starts to be interesting...
    }
}
