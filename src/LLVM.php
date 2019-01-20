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
        $this->ffi = FFI::cdef("

typedef int LLVMBool;

 
 /* Opaque types. */
 
 /**
  * LLVM uses a polymorphic type hierarchy which C cannot represent, therefore
  * parameters must be passed as base types. Despite the declared types, most
  * of the functions provided operate only on branches of the type hierarchy.
  * The declared parameter names are descriptive and specify which type is
  * required. Additionally, each type hierarchy is documented along with the
  * functions that operate upon it. For more detail, refer to LLVM's C++ code.
  * If in doubt, refer to Core.cpp, which performs parameter downcasts in the
  * form unwrap<RequiredType>(Param).
  */
 
 /**
  * Used to pass regions of memory through LLVM interfaces.
  *
  * @see llvm::MemoryBuffer
  */
 typedef struct LLVMOpaqueMemoryBuffer *LLVMMemoryBufferRef;
 typedef struct LLVMOpaqueContext *LLVMContextRef;
 typedef struct LLVMOpaqueModule *LLVMModuleRef;
 typedef struct LLVMOpaqueType *LLVMTypeRef; 
 typedef struct LLVMOpaqueValue *LLVMValueRef;
 typedef struct LLVMOpaqueBasicBlock *LLVMBasicBlockRef;
 typedef struct LLVMOpaqueMetadata *LLVMMetadataRef;
 typedef struct LLVMOpaqueNamedMDNode *LLVMNamedMDNodeRef;
 typedef struct LLVMOpaqueValueMetadataEntry LLVMValueMetadataEntry;
 typedef struct LLVMOpaqueBuilder *LLVMBuilderRef;
 typedef struct LLVMOpaqueDIBuilder *LLVMDIBuilderRef;
 typedef struct LLVMOpaqueModuleProvider *LLVMModuleProviderRef;
 typedef struct LLVMOpaquePassManager *LLVMPassManagerRef;
 typedef struct LLVMOpaquePassRegistry *LLVMPassRegistryRef;
 typedef struct LLVMOpaqueUse *LLVMUseRef;
 typedef struct LLVMOpaqueAttributeRef *LLVMAttributeRef;
 typedef struct LLVMOpaqueDiagnosticInfo *LLVMDiagnosticInfoRef;
 typedef struct LLVMComdat *LLVMComdatRef;
 typedef struct LLVMOpaqueModuleFlagEntry LLVMModuleFlagEntry;
 typedef struct LLVMOpaqueJITEventListener *LLVMJITEventListenerRef;
 
LLVMTypeRef LLVMInt32Type(void);
LLVMModuleRef LLVMModuleCreateWithName ( const char *  ModuleID);
LLVMTypeRef LLVMFunctionType(LLVMTypeRef ReturnType,LLVMTypeRef *ParamTypes, unsigned ParamCount,LLVMBool IsVarArg);
 LLVMValueRef LLVMAddFunction(LLVMModuleRef M, const char *Name,
  LLVMTypeRef FunctionTy);
LLVMBasicBlockRef LLVMAppendBasicBlockInContext(LLVMContextRef C,
                                                 LLVMValueRef FnRef,
                                                 const char *Name);
LLVMBasicBlockRef LLVMAppendBasicBlock(LLVMValueRef FnRef, const char *Name);  
LLVMBuilderRef LLVMCreateBuilderInContext(LLVMContextRef C);
LLVMBuilderRef LLVMCreateBuilder(void);
void LLVMPositionBuilderAtEnd(LLVMBuilderRef Builder, LLVMBasicBlockRef Block);
LLVMValueRef LLVMBuildAdd(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMGetParam(LLVMValueRef FnRef, unsigned index);
LLVMValueRef LLVMBuildRet(LLVMBuilderRef B, LLVMValueRef V);

LLVMValueRef LLVMGetNamedFunction(LLVMModuleRef M, const char *Name);

void LLVMDisposeMessage(char *Message);

void LLVMDumpModule(LLVMModuleRef M);
char *LLVMPrintModuleToString(LLVMModuleRef M);

/* http://llvm.org/doxygen/c_2Analysis_8h_source.html#l00035 */
typedef enum {
   LLVMAbortProcessAction, /* verifier will print to stderr and abort() */
   LLVMPrintMessageAction, /* verifier will print to stderr and return 1 */
   LLVMReturnStatusAction  /* verifier will just return 1 */
 } LLVMVerifierFailureAction;
 
 
 /* Verifies that a module is valid, taking the specified action if not.
    Optionally returns a human-readable description of any invalid constructs.
    OutMessage must be disposed with LLVMDisposeMessage. */
 LLVMBool LLVMVerifyModule(LLVMModuleRef M, LLVMVerifierFailureAction Action,
                           char **OutMessage);
 
 /* Verifies that a single function is valid, taking the specified action. Useful
    for debugging. */
 LLVMBool LLVMVerifyFunction(LLVMValueRef Fn, LLVMVerifierFailureAction Action);
 
 /* Open up a ghostview window that displays the CFG of the current function.
    Useful for debugging. */
 void LLVMViewFunctionCFG(LLVMValueRef Fn);
 void LLVMViewFunctionCFGOnly(LLVMValueRef Fn);

/* Execution engine*/

 typedef struct LLVMOpaqueGenericValue *LLVMGenericValueRef;
 typedef struct LLVMOpaqueExecutionEngine *LLVMExecutionEngineRef;
 typedef struct LLVMOpaqueMCJITMemoryManager *LLVMMCJITMemoryManagerRef;

void LLVMLinkInMCJIT();
void LLVMLinkInInterpreter(void);

LLVMGenericValueRef LLVMRunFunction(LLVMExecutionEngineRef EE, LLVMValueRef F,
                                     unsigned NumArgs,
  LLVMGenericValueRef *Args);
  
// http://llvm.org/doxygen/group__LLVMCExecutionEngine.html



/* Target.h*/

static inline LLVMBool LLVMInitializeNativeTarget(void);

/* BitWriter.cpp */
int LLVMWriteBitcodeToFile(LLVMModuleRef M, const char *Path);

/* ExecutionEngineBindings.cpp */
LLVMBool LLVMCreateInterpreterForModule(LLVMExecutionEngineRef *OutInterp, LLVMModuleRef M, char **OutError);
LLVMGenericValueRef LLVMCreateGenericValueOfInt(LLVMTypeRef Ty, unsigned long long N, LLVMBool IsSigned);
unsigned long long LLVMGenericValueToInt(LLVMGenericValueRef GenValRef, LLVMBool IsSigned);

    ",
            "/usr/lib/llvm-6.0/lib/libLLVM.so");


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

    private function wrap(string $type, $item)
    {
        return $type::marshal($item);
    }

    private function unwrap($item)
    {
        return $item->demarshal($this->ffi);
    }
}
