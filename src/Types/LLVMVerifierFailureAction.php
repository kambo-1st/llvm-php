<?php

namespace Kambo\LLVM\Types;

use FFI\CData;

/**
 * Represents LLVMVerifierFailureAction enum
 *
 * @author  Bohuslav Simek <bohuslav@simek.si>
 * @license BSD
 */
class LLVMVerifierFailureAction extends BaseRef
{
    private $value;

    // TODO is there a better way?
    private const LLVM_ABORT_PROCESS_ACTION = 0;
    private const LLVM_PRINT_MESSAGE_ACTION = 1;
    private const LLVM_RETURN_STATUS_ACTION = 2;

    private function __construct()
    {
    }

    /**
     * Verifier will print to stderr and abort().
     *
     * @return self
     */
    public static function LLVMAbortProcessAction() : self
    {
        return self::createValue(self::LLVM_ABORT_PROCESS_ACTION);
    }

    /**
     * Verifier will print to stderr and return 1.
     *
     * @return self
     */
    public static function LLVMPrintMessageAction() : self
    {
        return self::createValue(self::LLVM_PRINT_MESSAGE_ACTION);
    }

    /**
     * Verifier will just return 1.
     *
     * @return self
     */
    public static function LLVMReturnStatusAction() : self
    {
        return self::createValue(self::LLVM_RETURN_STATUS_ACTION);
    }

    /**
     * Create enum value
     *
     * @param string $name enum value
     *
     * @return self
     */
    private static function createValue(string $value) : self
    {
        $newInstance        = new self;
        $newInstance->value = $value;

        return $newInstance;
    }

    /**
     * Demarshal data into php FFI format
     *
     * @return mixed
     */
    public function demarshal($ffi = null)
    {
        return $this->value;
    }
}
