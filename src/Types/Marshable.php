<?php

namespace Kambo\LLVM\Types;

use FFI\CData;

/**
 * Interface marking marshable data types
 *
 * @author  Bohuslav Simek <bohuslav@simek.si>
 * @license BSD
 */
interface Marshable
{
    /**
     * Marshal data into php format
     *
     * @param FFI\CData $ffiStructure LLVM wrapper
     *
     * @return self
     */
    public static function marshal(CData $ffiStructure);

    /**
     * Demarshal data into php FFI format
     *
     * @return FFI\CData
     */
    public function demarshal() : CData;
}
