<?php

namespace Kambo\Tests\LLVM\Intergration;

use PHPUnit\Framework\TestCase;
use Kambo\LLVM\LLVM;

/**
 * Unit tests for class Kambo\LLVM\LLVM
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
    public function testPrintModuleToString(): void
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

        $tmp = $llvm->LLVMBuildAdd($builder, $llvm->LLVMGetParam($sum, 0), $llvm->LLVMGetParam($sum, 1), 'tmp');

        $llvm->LLVMBuildRet($builder, $tmp);

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
}
