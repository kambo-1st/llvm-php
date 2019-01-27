<?php

namespace Kambo\LLVM;

use FFI;
use Kambo\LLVM\Types\LLVMBasicBlockRef;
use Kambo\LLVM\Types\LLVMBuilderRef;
use Kambo\LLVM\Types\LLVMExecutionEngineRef;
use Kambo\LLVM\Types\LLVMGenericValueRef;
use Kambo\LLVM\Types\LLVMModuleRef;
use Kambo\LLVM\Types\LLVMTypeRef;
use Kambo\LLVM\Types\LLVMValueRef;
use Kambo\LLVM\Types\Marshable;
use Kambo\LLVM\Assert\Assertion;

/**
 * Simple wrapper around LLVM c api
 *
 * @author  Bohuslav Simek <bohuslav@simek.si>
 * @license BSD
 */
class LLVM
{
    private $ffi;

    public function __construct()
    {
        $this->ffi = FFI::cdef(
            file_get_contents(__DIR__ . '/Headers/llvm.h'),
            'libLLVM-6.0.so'
        );
    }

    /**
     * Create a new, empty module in the global context.
     * This is equivalent to calling LLVMModuleCreateWithNameInContext with LLVMGetGlobalContext()
     * as the context parameter.
     *
     * @param string $moduleName Module name
     *
     * @return LLVMModuleRef Reference to the module
     */
    public function LLVMModuleCreateWithName(string $moduleName) : LLVMModuleRef
    {
        $ffiStructure = $this->ffi->LLVMModuleCreateWithName($moduleName);

        return $this->wrap(LLVMModuleRef::class, $ffiStructure);
    }

    /**
     * Obtain a function type consisting of a specified signature.
     *
     * The function is defined as a tuple of a return Type, a list of parameter types,
     * and whether the function is variadic.
     *
     * @param LLVMTypeRef   $returnType Return type of the function
     * @param LLVMTypeRef[] $paramTypes Data types of the parameters
     * @param int           $paramCount Number of function parameters
     * @param bool          $isVarArg   Flag for marking function variadic
     *
     * @return LLVMTypeRef Reference to the function
     */
    public function LLVMFunctionType(
        LLVMTypeRef $returnType,
        array $paramTypes,
        int $paramCount,
        bool $isVarArg
    ) : LLVMTypeRef {
        Assertion::allIsInstanceOf(
            $paramTypes,
            LLVMTypeRef::class,
            'All provided parameters must be instances of '.LLVMTypeRef::class
        );
        Assertion::count(
            $paramTypes,
            $paramCount,
            'Number of the provided parameters must be same as their count.'
        );

        $paramTypesFfi = $this->createArray('LLVMTypeRef', $paramCount, $paramTypes);

        $ffiStructure = $this->ffi->LLVMFunctionType($returnType->demarshal(), $paramTypesFfi, $paramCount, $isVarArg);

        return $this->wrap(LLVMTypeRef::class, $ffiStructure);
    }

    /**
     * Obtain a reference to 32 bit integer type
     *
     * @return LLVMTypeRef Reference to 32 bit integer type
     */
    public function LLVMInt32Type() : LLVMTypeRef
    {
        $ffiStructure = $this->ffi->LLVMInt32Type();

        return $this->wrap(LLVMTypeRef::class, $ffiStructure);
    }

    /**
     * Add a function to a module under a specified name.
     *
     * @param LLVMModuleRef $module       Module to which the function will be add
     * @param string        $name         Name of the function
     * @param LLVMTypeRef   $functionType Reference to the function type which will be add
     *
     * @return LLVMValueRef Reference to the added function
     */
    public function LLVMAddFunction(LLVMModuleRef $module, string $name, LLVMTypeRef $functionType) : LLVMValueRef
    {
        $ffiStructure = $this->ffi->LLVMAddFunction($module->demarshal(), $name, $functionType->demarshal());

        return $this->wrap(LLVMValueRef::class, $ffiStructure);
    }

    /**
     * Creates LLVM basic block builder for creating instructions and inserting them into a basic block.
     *
     * @return LLVMBuilderRef LLVM basic block builder
     */
    public function LLVMCreateBuilder() : LLVMBuilderRef
    {
        $ffiStructure = $this->ffi->LLVMCreateBuilder();

        return $this->wrap(LLVMBuilderRef::class, $ffiStructure);
    }

    /**
     * Append a basic block to the end of a function using the global context.
     *
     * @param LLVMValueRef $function Function to which the block will be append
     * @param string       $name     Name of the block
     *
     * @return LLVMBasicBlockRef A basic block of instructions in LLVM IR
     */
    public function LLVMAppendBasicBlock(LLVMValueRef $function, string $name) : LLVMBasicBlockRef
    {
        $ffiStructure = $this->ffi->LLVMAppendBasicBlock($function->demarshal(), $name);

        return $this->wrap(LLVMBasicBlockRef::class, $ffiStructure);
    }

    /**
     * Specifies that created instructions should be appended to the end of the specified block.
     *
     * @param LLVMBuilderRef    $builder LLVM basic block builder
     * @param LLVMBasicBlockRef $block   A basic block of instructions in LLVM IR
     *
     * @return void
     */
    public function LLVMPositionBuilderAtEnd(LLVMBuilderRef $builder, LLVMBasicBlockRef $block) : void
    {
        $this->ffi->LLVMPositionBuilderAtEnd($this->unwrap($builder), $this->unwrap($block));
    }

    /**
     * Build add operation.
     *
     * @param LLVMBuilderRef $builder LLVM basic block builder
     * @param LLVMValueRef   $LHS     Left-hand side
     * @param LLVMValueRef   $RHS     Right-hand side
     * @param string         $name    Result variable name
     *
     * @return LLVMValueRef Reference to the binary operation
     */
    public function LLVMBuildAdd(
        LLVMBuilderRef $builder,
        LLVMValueRef $LHS,
        LLVMValueRef $RHS,
        string $name
    ) : LLVMValueRef {
        $ffiStructure = $this->ffi->LLVMBuildAdd(
            $this->unwrap($builder),
            $this->unwrap($LHS),
            $this->unwrap($RHS),
            $name
        );

        return $this->wrap(LLVMValueRef::class, $ffiStructure);
    }

    /**
     * Obtain the parameter at the specified index.
     * Parameters are indexed from 0.
     *
     * @param LLVMValueRef $fnRef Function from which the parameter will be obtain
     * @param int          $index Parameter index
     *
     * @return LLVMValueRef Reference to the parameter
     */
    public function LLVMGetParam(LLVMValueRef $fnRef, int $index) : LLVMValueRef
    {
        $ffiStructure = $this->ffi->LLVMGetParam(
            $this->unwrap($fnRef),
            $index
        );

        return $this->wrap(LLVMValueRef::class, $ffiStructure);
    }

    /**
     * Create a 'ret <val>' instruction.
     *
     * @param LLVMBuilderRef $builder LLVM basic block builder
     * @param LLVMValueRef   $value   Value which should be returned
     *
     * @return LLVMValueRef Reference to the binary operation
     */
    public function LLVMBuildRet(LLVMBuilderRef $builder, LLVMValueRef $value) : LLVMValueRef
    {
        $ffiStructure = $this->ffi->LLVMBuildRet(
            $this->unwrap($builder),
            $this->unwrap($value)
        );

        return $this->wrap(LLVMValueRef::class, $ffiStructure);
    }

    /**
     * Return a string representation of the module.
     *
     * @param LLVMModuleRef $module Module which will be converted into string
     *
     * @return string String representation of the module
     */
    public function LLVMPrintModuleToString(LLVMModuleRef $module) : string
    {
        $ffiStructure = $this->ffi->LLVMPrintModuleToString(
            $this->unwrap($module)
        );

        // TODO Use LLVMDisposeMessage to free the string?
        return FFI::string($ffiStructure);
    }

    /**
     * Link in interpreter.
     * This is dummy function kept here only for compatibility reasons.
     *
     * @return void
     */
    public function LLVMLinkInInterpreter() : void
    {
    }

    /**
     * Create interpreter for provided module.
     *
     * @param LLVMExecutionEngineRef $outInterp Reference to execution engine
     * @param LLVMModuleRef          $module    Module for which the interpreter will be created
     * @param string                 $outError  Error message
     *
     * @return bool True if the creation
     */
    public function LLVMCreateInterpreterForModule(
        LLVMExecutionEngineRef $outInterp,
        LLVMModuleRef $module,
        ?string &$outError
    ) : bool {
        $unWrap = $this->unwrap($outInterp);

        $enginePointer = FFI::addr($unWrap);
        $ffiStructure  = $this->ffi->LLVMCreateInterpreterForModule($enginePointer, $this->unwrap($module), $outError);

        return (bool)$ffiStructure;
    }

    /**
     * Obtain a Function value from a Module by its name.
     *
     * @param LLVMModuleRef $module Module from which the function will be gotten
     * @param string        $name   Function name
     *
     * @return LLVMValueRef|null Found function or null, if nothing is found
     */
    public function LLVMGetNamedFunction(LLVMModuleRef $module, string $name) : ?LLVMValueRef
    {
        $ffiStructure = $this->ffi->LLVMGetNamedFunction($this->unwrap($module), $name);

        return ($ffiStructure === null) ? null : $this->wrap(LLVMValueRef::class, $ffiStructure);
    }

    /**
     * Convert integer into the generic value
     *
     * @param LLVMTypeRef $type     Target value type
     * @param int         $number   Value which should be converted
     * @param bool        $isSigned Indicates that the value is signed
     *
     * @return LLVMValueRef Integer converted into generic value
     */
    public function LLVMCreateGenericValueOfInt(LLVMTypeRef $type, int $number, bool $isSigned) : LLVMValueRef
    {
        $ffiStructure = $this->ffi->LLVMCreateGenericValueOfInt($this->unwrap($type), $number, $isSigned);

        return $this->wrap(LLVMValueRef::class, $ffiStructure);
    }

    /**
     * Execute the specified function with the specified arguments, and return the result.
     *
     * @param LLVMExecutionEngineRef $executionEngine Reference to execution engine
     * @param LLVMValueRef           $function        Function which will be executed
     * @param int                    $numArgs         Number of function arguments
     * @param array                  $args            Function arguments
     *
     * @return LLVMGenericValueRef
     */
    public function LLVMRunFunction($executionEngine, $function, $numArgs, $args) : LLVMGenericValueRef
    {
        //Assertion::allIsInstanceOf($args, LLVMValueRef::class); segfaulting on PHP master, dunno why...
        Assertion::count($args, $numArgs);

        $inputValues = $this->createArray('LLVMGenericValueRef', $numArgs, $args);

        $ffiStructure = $this->ffi->LLVMRunFunction(
            $this->unwrap($executionEngine),
            $this->unwrap($function),
            $numArgs,
            $inputValues
        );

        return $this->wrap(LLVMGenericValueRef::class, $ffiStructure);
    }

    /**
     * Convert generic value into the integer
     *
     * @param LLVMGenericValueRef $genValRef Generic value
     * @param bool                $isSigned  Indicates that the value is signed
     *
     * @return int generic value converted into integer
     */
    public function LLVMGenericValueToInt(LLVMGenericValueRef $genValRef, bool $isSigned) : int
    {
        return $this->ffi->LLVMGenericValueToInt($this->unwrap($genValRef), $isSigned);
    }

    private function wrap(string $type, $item) : Marshable
    {
        return $type::marshal($item);
    }

    private function unwrap(Marshable $item)
    {
        return $item->demarshal($this->ffi);
    }

    private function createArray(string $type, int $size, array $items = [])
    {
        $arrayType       = $this->ffi->type($type);
        $arrayDefinition = $this->ffi::arrayType($arrayType, [$size]);
        $array           = $this->ffi->new($arrayDefinition);

        $index = 0;
        foreach ($items as $item) {
            $array[$index] = $item->demarshal();
            $index++;
        }

        return $array;
    }
}
