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

/**
 * Simple wrapper around LLVM c api
 *
 * @author  Bohuslav Simek <bohuslav@simek.si>
 * @license MIT
 */
class LLVM
{
    private $ffi;

    public function __construct()
    {
        // Not ideal but suffice for POC
        $this->ffi = FFI::cdef(
            file_get_contents(__DIR__ . '/Headers/llvm.h'),
            '/usr/lib/llvm-6.0/lib/libLLVM.so'
        );
    }

    public function LLVMModuleCreateWithName(string $moduleName) : LLVMModuleRef
    {
        $ffiStructure = $this->ffi->LLVMModuleCreateWithName($moduleName);
        return LLVMModuleRef::marshal($ffiStructure);
    }

    public function LLVMFunctionType(LLVMTypeRef $returnType, array $paramTypes, int $paramCount, bool $isVarArg) : LLVMTypeRef
    {
        // TODO sanity check
        // TODO use instead static function FFI::arrayType(FFI\CType $type, array $dims): FFI\CType
        $paramTypesFfi = $this->ffi->new("LLVMTypeRef param_types[".$paramCount."]");

        foreach ($paramTypes as $pos => $paramType) {
            // Index must be provided
            $paramTypesFfi[$pos]  = $paramType->demarshal();
        }

        $ffiStructure = $this->ffi->LLVMFunctionType($returnType->demarshal(), $paramTypesFfi, $paramCount, $isVarArg);

        return LLVMTypeRef::marshal($ffiStructure);
    }

    public function LLVMInt32Type() : LLVMTypeRef
    {
        $ffiStructure = $this->ffi->LLVMInt32Type();

        return LLVMTypeRef::marshal($ffiStructure);
    }

    public function LLVMAddFunction(LLVMModuleRef $module, string $name, LLVMTypeRef $functionType) : LLVMValueRef {
        $ffiStructure = $this->ffi->LLVMAddFunction($module->demarshal(), $name, $functionType->demarshal());

        return LLVMValueRef::marshal($ffiStructure);
    }

    public function LLVMCreateBuilder() : LLVMBuilderRef {
        $ffiStructure = $this->ffi->LLVMCreateBuilder();

        return LLVMBuilderRef::marshal($ffiStructure);
    }

    public function LLVMAppendBasicBlock(LLVMValueRef $function, string $name) : LLVMBasicBlockRef
    {
        $ffiStructure = $this->ffi->LLVMAppendBasicBlock($function->demarshal(), $name);

        return LLVMBasicBlockRef::marshal($ffiStructure);
    }

    public function LLVMPositionBuilderAtEnd(LLVMBuilderRef $builder, LLVMBasicBlockRef $block) : void
    {
        $this->ffi->LLVMPositionBuilderAtEnd($this->unwrap($builder), $this->unwrap($block));
    }

    public function LLVMBuildAdd(LLVMBuilderRef $builder, LLVMValueRef $LHS, LLVMValueRef $RHS, string $name) : LLVMValueRef
    {
        $ffiStructure = $this->ffi->LLVMBuildAdd(
            $this->unwrap($builder),
            $this->unwrap($LHS),
            $this->unwrap($RHS),
            $name
        );

        return $this->wrap(LLVMValueRef::class, $ffiStructure);
    }

    public function LLVMGetParam(LLVMValueRef $fnRef, int $index) : LLVMValueRef
    {
        $ffiStructure = $this->ffi->LLVMGetParam(
            $this->unwrap($fnRef),
            $index
        );

        return $this->wrap(LLVMValueRef::class, $ffiStructure);
    }

    public function LLVMBuildRet(LLVMBuilderRef $builder, LLVMValueRef $value) : LLVMValueRef
    {
        $ffiStructure = $this->ffi->LLVMBuildRet(
            $this->unwrap($builder),
            $this->unwrap($value)
        );

        return $this->wrap(LLVMValueRef::class, $ffiStructure);
    }

    public function LLVMPrintModuleToString(LLVMModuleRef $module) : string
    {
        $ffiStructure = $this->ffi->LLVMPrintModuleToString(
            $this->unwrap($module)
        );

        return FFI::string($ffiStructure);
    }

    public function LLVMLinkInInterpreter() : void
    {
        $this->ffi->LLVMLinkInInterpreter();
    }

    public function LLVMCreateInterpreterForModule(LLVMExecutionEngineRef $outInterp, LLVMModuleRef $module, $outError)
    {
        $unWrap = $this->unwrap($outInterp);

        $enginePointer = FFI::addr($unWrap);

        // TODO this should return LLVMBool
        $this->ffi->LLVMCreateInterpreterForModule($enginePointer, $this->unwrap($module), $outError);
    }

    public function LLVMGetNamedFunction(LLVMModuleRef $module, string $name) : LLVMValueRef
    {
        $ffiStructure = $this->ffi->LLVMGetNamedFunction($this->unwrap($module), $name);

        return $this->wrap(LLVMValueRef::class, $ffiStructure);
    }

    public function LLVMCreateGenericValueOfInt(LLVMTypeRef $type, int $number, bool $isSigned) : LLVMValueRef
    {
        $ffiStructure = $this->ffi->LLVMCreateGenericValueOfInt($this->unwrap($type), $number, $isSigned);

        return $this->wrap(LLVMValueRef::class, $ffiStructure);
    }

    public function LLVMRunFunction($executionEngine, $function, $numArgs, $args) : LLVMGenericValueRef
    {
        $inputValues = $this->ffi->new("LLVMGenericValueRef args[".$numArgs."]");

        foreach ($args as $pos => $paramType) {
            // Index must be provided
            $inputValues[$pos]  = $paramType->demarshal();
        }

        $ffiStructure = $this->ffi->LLVMRunFunction(
            $this->unwrap($executionEngine),
            $this->unwrap($function),
            $numArgs,
            $inputValues
        );

        return $this->wrap(LLVMGenericValueRef::class, $ffiStructure);
    }

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
}
