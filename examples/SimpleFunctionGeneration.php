<?php

require '../vendor/autoload.php';

use Kambo\LLVM\LLVM;

/**
 * Generates and print LLVM equivalent of:
 *
 * sum(int $a, int $b) : int {
 *     return $a + $b;
 * }
 */

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

echo $llvm->LLVMPrintModuleToString($module);
