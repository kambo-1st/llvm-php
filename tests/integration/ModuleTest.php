<?php

namespace Kambo\Tests\LLVM\Integration;

use PHPUnit\Framework\TestCase;

use Kambo\LLVM\LLVM;

use Kambo\LLVM\Types\LLVMExecutionEngineRef;
use Kambo\LLVM\Types\LLVMModuleRef;
use Kambo\LLVM\Types\LLVMVerifierFailureAction;

use Kambo\LLVM\Assert\InvalidArgumentException;

/**
 * Integration tests for class Kambo\LLVM\LLVM
 *
 * @author Bohuslav Simek <bohuslav@simek.si>
 */
class ModuleTest extends TestCase
{
    /**
     * Tests printing module into the string
     *
     * @return void
     */
    public function testPrintModuleToString() : void
    {
        $llvm   = new LLVM();
        $module = $this->getTestedModule($llvm);

        $moduleString = $llvm->LLVMPrintModuleToString($module);

        $expectedModuleString = <<<EOT
; ModuleID = 'my_module'
source_filename = "my_module"

define i32 @sum(i32, i32) {
entry:
  %tmp = add i32 %0, %1
  ret i32 %tmp
}

EOT;

        $this->assertEquals($expectedModuleString, $moduleString);
    }

    /**
     * Tests execution of the module
     *
     * @return void
     */
    public function testInterpretation() : void
    {
        $llvm   = new LLVM();
        $module = $this->getTestedModule($llvm);

        $llvm->LLVMLinkInInterpreter();
        $executionEngine = new LLVMExecutionEngineRef();

        $error = null;
        $llvm->LLVMCreateInterpreterForModule($executionEngine, $module, $error);

        $namedFunction = $llvm->LLVMGetNamedFunction($module, 'sum');

        $inputValues = [
            $llvm->LLVMCreateGenericValueOfInt($llvm->LLVMInt32Type(), 12, 0),
            $llvm->LLVMCreateGenericValueOfInt($llvm->LLVMInt32Type(), 15, 0)
        ];

        $result          = $llvm->LLVMRunFunction($executionEngine, $namedFunction, 2, $inputValues);
        $convertedResult = $llvm->LLVMGenericValueToInt($result, false);

        $this->assertEquals(27, $convertedResult);
    }

    /**
     * Tests verifying module, module is valid
     *
     * @return void
     */
    public function testLLVMVerifyModule() : void
    {
        $llvm   = new LLVM();
        $module = $this->getTestedModule($llvm);

        $error  = null;
        $result = $llvm->LLVMVerifyModule($module, LLVMVerifierFailureAction::LLVMReturnStatusAction(), $error);

        $this->assertFalse($result);
    }

    /**
     * Tests verifying module, module is invalid
     *
     * @return void
     */
    public function testLLVMVerifyModuleInvalid() : void
    {
        $llvm   = new LLVM();
        $module = $llvm->LLVMModuleCreateWithName("my_module");

        $paramTypes = [
            $llvm->LLVMInt32Type(),
            $llvm->LLVMInt32Type()
        ];

        $retType  = $llvm->LLVMFunctionType($llvm->LLVMInt32Type(), $paramTypes, 2, 0);

        $sum   = $llvm->LLVMAddFunction($module, 'sum', $retType);
        $entry = $llvm->LLVMAppendBasicBlock($sum, 'entry');

        $builder = $llvm->LLVMCreateBuilder();
        $llvm->LLVMPositionBuilderAtEnd($builder, $entry);

        $error  = null;
        $result = $llvm->LLVMVerifyModule($module, LLVMVerifierFailureAction::LLVMReturnStatusAction(), $error);

        $this->assertTrue($result);
        $this->assertEquals("Basic Block in function 'sum' does not have terminator!\nlabel %entry\n", $error);
    }

    /**
     * Tests verifying function, function is valid.
     *
     * @return void
     */
    public function testLLVMVerifyFunction() : void
    {
        $llvm   = new LLVM();
        $module = $this->getTestedModule($llvm);

        $function = $llvm->LLVMGetNamedFunction($module, 'sum');
        $result   = $llvm->LLVMVerifyFunction($function, LLVMVerifierFailureAction::LLVMReturnStatusAction());

        $this->assertFalse($result);
    }

    /**
     * Tests verifying function, function is invalid.
     *
     * @return void
     */
    public function testLLVMVerifyFunctionInvalid() : void
    {
        $llvm   = new LLVM();
        $module = $llvm->LLVMModuleCreateWithName("my_module");

        $paramTypes = [
            $llvm->LLVMInt32Type(),
            $llvm->LLVMInt32Type()
        ];

        $retType  = $llvm->LLVMFunctionType($llvm->LLVMInt32Type(), $paramTypes, 2, 0);

        $sum   = $llvm->LLVMAddFunction($module, 'sum', $retType);
        $entry = $llvm->LLVMAppendBasicBlock($sum, 'entry');

        $builder = $llvm->LLVMCreateBuilder();
        $llvm->LLVMPositionBuilderAtEnd($builder, $entry);

        $result = $llvm->LLVMVerifyFunction($sum, LLVMVerifierFailureAction::LLVMReturnStatusAction());

        $this->assertTrue($result);
    }

    /**
     * Tests providing invalid parameters count to the method LLVMFunctionType
     *
     * @return void
     */
    public function testInvalidParamCount() : void
    {
        $llvm       = new LLVM();
        $paramTypes = [
            $llvm->LLVMInt32Type(),
            $llvm->LLVMInt32Type()
        ];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Number of the provided parameters must be same as their count.');

        $llvm->LLVMFunctionType($llvm->LLVMInt32Type(), $paramTypes, 5, 0);
    }

    /**
     * Tests getting non existing function
     *
     * @return void
     */
    public function testLLVMGetNamedFunctionNonExistingFunction() : void
    {
        $llvm   = new LLVM();
        $module = $this->getTestedModule($llvm);

        $llvm->LLVMLinkInInterpreter();
        $executionEngine = new LLVMExecutionEngineRef();

        $error = null;
        $llvm->LLVMCreateInterpreterForModule($executionEngine, $module, $error);

        $namedFunction = $llvm->LLVMGetNamedFunction($module, 'non existing function');

        $this->assertNull($namedFunction);
    }

    /**
     * Generate LLVM equivalent of:
     *
     * int sum(int a, int b) {
     *     return a + b;
     * }
     *
     * @param LLVM $llvm LLVM wrapper
     *
     * @return LLVMModuleRef
     */
    private function getTestedModule(LLVM $llvm) : LLVMModuleRef
    {
        $module = $llvm->LLVMModuleCreateWithName("my_module");

        $paramTypes = [
            $llvm->LLVMInt32Type(),
            $llvm->LLVMInt32Type()
        ];

        $retType  = $llvm->LLVMFunctionType($llvm->LLVMInt32Type(), $paramTypes, 2, 0);

        $sum   = $llvm->LLVMAddFunction($module, 'sum', $retType);
        $entry = $llvm->LLVMAppendBasicBlock($sum, 'entry');

        $builder = $llvm->LLVMCreateBuilder();
        $llvm->LLVMPositionBuilderAtEnd($builder, $entry);

        $tmp = $llvm->LLVMBuildAdd($builder, $llvm->LLVMGetParam($sum, 0), $llvm->LLVMGetParam($sum, 1), 'tmp');

        $llvm->LLVMBuildRet($builder, $tmp);

        return $module;
    }
}
