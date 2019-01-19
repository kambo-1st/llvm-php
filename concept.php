<?php

$start = microtime(true);

$libc = FFI::cdef("

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

", "/usr/lib/llvm-6.0/lib/libLLVM.so");

// https://qiita.com/JunSuzukiJapan/items/88c5fec58dddb0522cca
// https://lowlevelbits.org/how-to-use-llvm-api-with-swift.-addendum/
// https://qiita.com/JunSuzukiJapan/items/88c5fec58dddb0522cca
echo microtime(true) - $start."\n";

$module = $libc->LLVMModuleCreateWithName("my_module");

$paramTypes    = $libc->new("LLVMTypeRef param_types[2]");
$paramTypes[0] = $libc->LLVMInt32Type();
$paramTypes[1] = $libc->LLVMInt32Type();

$retType  = $libc->LLVMFunctionType($libc->LLVMInt32Type(), $paramTypes, 2, 0);

$sum   = $libc->LLVMAddFunction($module, 'sum', $retType);
$entry = $libc->LLVMAppendBasicBlock($sum, 'entry');

$builder = $libc->LLVMCreateBuilder();
$libc->LLVMPositionBuilderAtEnd($builder, $entry);

$tmp = $libc->LLVMBuildAdd($builder, $libc->LLVMGetParam($sum, 0), $libc->LLVMGetParam($sum, 1), 'tmp');


$libc->LLVMBuildRet($builder, $tmp);

$error = null;//$libc->new("char *error = NULL");

$LLVMAbortProcessAction = $libc->new("LLVMVerifierFailureAction LLVMAbortProcessAction");

$libc->LLVMVerifyModule($module, $LLVMAbortProcessAction, $error);

$libc->LLVMDisposeMessage($error);


$engine = $libc->new("LLVMExecutionEngineRef engine");

$error = null;

//$libc->LLVMLinkInMCJIT();

// $libc->LLVMInitializeNativeTarget(); //http://lists.llvm.org/pipermail/llvm-dev/2013-February/059544.html
// readelf -Ws /usr/lib/llvm-6.0/lib/libLLVM.so > export.txt
// https://github.com/paulsmith/getting-started-llvm-c-api/blob/master/sum.c

// Print
$libc->LLVMDumpModule($module);

// Print to string
$moduleString = $libc->LLVMPrintModuleToString($module);
$moduleString = FFI::string($moduleString);

$libc->LLVMWriteBitcodeToFile($module, 'sum.bc');

$libc->LLVMLinkInInterpreter();


$error = null;//$libc->new("char *error");

$enginePointer = FFI::addr($engine); // LLVMCreateInterpreterForModule expect pointer to engine
$libc->LLVMCreateInterpreterForModule($enginePointer, $module, $error);

$namedFunction = $libc->LLVMGetNamedFunction($module, 'sum');

//LLVMGenericValueRef args[]
$inputValues = $libc->new("LLVMGenericValueRef args[2]");
//$inputValuesPointer = FFI::addr($inputValues);
/*$inputValues[0] = 10;
$inputValues[1] = 10;*/

$inputValues[0] = $libc->LLVMCreateGenericValueOfInt($libc->LLVMInt32Type(), 12, 0);
$inputValues[1] = $libc->LLVMCreateGenericValueOfInt($libc->LLVMInt32Type(), 15, 0);
$result         = $libc->LLVMRunFunction($engine, $namedFunction, 2, $inputValues);

var_dump($libc->LLVMGenericValueToInt($result, 0));

/*
LLVMDisposeBuilder(builder);
LLVMDisposeExecutionEngine(engine);
*/
$time_elapsed_secs = microtime(true) - $start;

echo $time_elapsed_secs;

/*var_dump($error);


var_dump($entry);*/


"
const char * LLVMGetTarget(LLVMModuleRef M);
const char *LLVMGetStringAttributeKind(LLVMAttributeRef A, unsigned *Length);
const char *LLVMGetStringAttributeValue(LLVMAttributeRef A, unsigned *Length);
const char *LLVMGetBasicBlockName(LLVMBasicBlockRef BB);
const char *LLVMGetBufferStart(LLVMMemoryBufferRef MemBuf);
const char *LLVMModuleFlagEntriesGetKey(LLVMModuleFlagEntry *Entries, unsigned Index, size_t *Len);
const char *LLVMGetDataLayoutStr(LLVMModuleRef M);
const char *LLVMGetDataLayout(LLVMModuleRef M);
const char *LLVMGetModuleIdentifier(LLVMModuleRef M, size_t *Len);
const char *LLVMGetSourceFileName(LLVMModuleRef M, size_t *Len);
const char *LLVMGetModuleInlineAsm(LLVMModuleRef M, size_t *Len);
const char *LLVMGetNamedMetadataName(LLVMNamedMDNodeRef NMD, size_t *NameLen);
const char *LLVMGetStructName(LLVMTypeRef Ty);
const char *LLVMGetAsString(LLVMValueRef C, size_t *Length);
const char *LLVMGetGC(LLVMValueRef Fn);
const char *LLVMGetSection(LLVMValueRef Global);
const char *LLVMGetMDString(LLVMValueRef V, unsigned *Length);
const char *LLVMGetValueName(LLVMValueRef Val);
const char *LLVMGetValueName2(LLVMValueRef Val, size_t *Length);
const char *LLVMGetDebugLocDirectory(LLVMValueRef Val, unsigned *Length);
const char *LLVMGetDebugLocFilename(LLVMValueRef Val, unsigned *Length);
const char *LLVMIntrinsicCopyOverloadedName(unsigned ID, LLVMTypeRef *ParamTypes, size_t ParamCount, size_t *NameLength);
const char *LLVMIntrinsicGetName(unsigned ID, size_t *NameLength);
const unsigned *LLVMGetIndices(LLVMValueRef Inst);
double LLVMConstRealGetDouble(LLVMValueRef ConstantVal, LLVMBool *LosesInfo);
char* LLVMPrintValueToString(LLVMValueRef Val);
char *LLVMCreateMessage(const char *Message);
char *LLVMGetDiagInfoDescription(LLVMDiagnosticInfoRef DI);
char *LLVMPrintModuleToString(LLVMModuleRef M);
char *LLVMPrintTypeToString(LLVMTypeRef Ty);
int LLVMHasMetadata(LLVMValueRef Inst);
int LLVMGetNumOperands(LLVMValueRef Val);
LLVMAtomicOrdering LLVMGetCmpXchgSuccessOrdering(LLVMValueRef CmpXchgInst);
LLVMAtomicOrdering LLVMGetCmpXchgFailureOrdering(LLVMValueRef CmpXchgInst);
LLVMAtomicOrdering LLVMGetOrdering(LLVMValueRef MemAccessInst);
LLVMAttributeRef LLVMCreateStringAttribute(LLVMContextRef C, const char *K, unsigned KLength, const char *V, unsigned VLength);
LLVMAttributeRef LLVMCreateEnumAttribute(LLVMContextRef C, unsigned KindID, uint64_t Val);
LLVMAttributeRef LLVMGetCallSiteStringAttribute(LLVMValueRef C, LLVMAttributeIndex Idx, const char *K, unsigned KLen);
LLVMAttributeRef LLVMGetCallSiteEnumAttribute(LLVMValueRef C, LLVMAttributeIndex Idx, unsigned KindID);
LLVMAttributeRef LLVMGetStringAttributeAtIndex(LLVMValueRef F, LLVMAttributeIndex Idx, const char *K, unsigned KLen);
LLVMAttributeRef LLVMGetEnumAttributeAtIndex(LLVMValueRef F, LLVMAttributeIndex Idx, unsigned KindID);
LLVMBasicBlockRef LLVMGetNextBasicBlock(LLVMBasicBlockRef BB);
LLVMBasicBlockRef LLVMGetPreviousBasicBlock(LLVMBasicBlockRef BB);
LLVMBasicBlockRef LLVMInsertBasicBlock(LLVMBasicBlockRef BBRef, const char *Name);
LLVMBasicBlockRef LLVMGetInsertBlock(LLVMBuilderRef Builder);
LLVMBasicBlockRef LLVMCreateBasicBlockInContext(LLVMContextRef C, const char *Name);
LLVMBasicBlockRef LLVMInsertBasicBlockInContext(LLVMContextRef C, LLVMBasicBlockRef BBRef, const char *Name);
LLVMBasicBlockRef LLVMAppendBasicBlockInContext(LLVMContextRef C, LLVMValueRef FnRef, const char *Name);
LLVMBasicBlockRef LLVMGetEntryBasicBlock(LLVMValueRef Fn);
LLVMBasicBlockRef LLVMGetFirstBasicBlock(LLVMValueRef Fn);
LLVMBasicBlockRef LLVMGetLastBasicBlock(LLVMValueRef Fn);
LLVMBasicBlockRef LLVMAppendBasicBlock(LLVMValueRef FnRef, const char *Name);
LLVMBasicBlockRef LLVMGetInstructionParent(LLVMValueRef Inst);
LLVMBasicBlockRef LLVMGetNormalDest(LLVMValueRef Invoke);
LLVMBasicBlockRef LLVMGetUnwindDest(LLVMValueRef Invoke);
LLVMBasicBlockRef LLVMGetIncomingBlock(LLVMValueRef PhiNode, unsigned Index);
LLVMBasicBlockRef LLVMGetSwitchDefaultDest(LLVMValueRef Switch);
LLVMBasicBlockRef LLVMGetSuccessor(LLVMValueRef Term, unsigned i);
LLVMBasicBlockRef LLVMValueAsBasicBlock(LLVMValueRef Val);
LLVMBool LLVMStartMultithreaded();
LLVMBool LLVMIsMultithreaded();
LLVMBool LLVMCreateMemoryBufferWithContentsOfFile(const char *Path, LLVMMemoryBufferRef *OutMemBuf, char **OutMessage);
LLVMBool LLVMIsEnumAttribute(LLVMAttributeRef A);
LLVMBool LLVMIsStringAttribute(LLVMAttributeRef A);
LLVMBool LLVMContextShouldDiscardValueNames(LLVMContextRef C);
LLVMBool LLVMCreateMemoryBufferWithSTDIN(LLVMMemoryBufferRef *OutMemBuf, char **OutMessage);
LLVMBool LLVMPrintModuleToFile(LLVMModuleRef M, const char *Filename, char **ErrorMessage);
LLVMBool LLVMInitializeFunctionPassManager(LLVMPassManagerRef FPM);
LLVMBool LLVMFinalizeFunctionPassManager(LLVMPassManagerRef FPM);
LLVMBool LLVMRunFunctionPassManager(LLVMPassManagerRef FPM, LLVMValueRef F);
LLVMBool LLVMRunPassManager(LLVMPassManagerRef PM, LLVMModuleRef M);
LLVMBool LLVMIsFunctionVarArg(LLVMTypeRef FunctionTy);
LLVMBool LLVMIsPackedStruct(LLVMTypeRef StructTy);
LLVMBool LLVMIsOpaqueStruct(LLVMTypeRef StructTy);
LLVMBool LLVMIsLiteralStruct(LLVMTypeRef StructTy);
LLVMBool LLVMTypeIsSized(LLVMTypeRef Ty);
LLVMBool LLVMIsAtomicSingleThread(LLVMValueRef AtomicInst);
LLVMBool LLVMIsConditional(LLVMValueRef Branch);
LLVMBool LLVMIsConstantString(LLVMValueRef C);
LLVMBool LLVMIsTailCall(LLVMValueRef Call);
LLVMBool LLVMHasPersonalityFn(LLVMValueRef Fn);
LLVMBool LLVMIsInBounds(LLVMValueRef GEP);
LLVMBool LLVMIsDeclaration(LLVMValueRef Global);
LLVMBool LLVMHasUnnamedAddr(LLVMValueRef Global);
LLVMBool LLVMIsThreadLocal(LLVMValueRef GlobalVar);
LLVMBool LLVMIsGlobalConstant(LLVMValueRef GlobalVar);
LLVMBool LLVMIsExternallyInitialized(LLVMValueRef GlobalVar);
LLVMBool LLVMIsCleanup(LLVMValueRef LandingPad);
LLVMBool LLVMGetVolatile(LLVMValueRef MemAccessInst);
LLVMBool LLVMIsConstant(LLVMValueRef Ty);
LLVMBool LLVMIsNull(LLVMValueRef Val);
LLVMBool LLVMIsUndef(LLVMValueRef Val);
LLVMBool LLVMValueIsBasicBlock(LLVMValueRef Val);
LLVMBool LLVMIntrinsicIsOverloaded(unsigned ID);
LLVMBuilderRef LLVMCreateBuilderInContext(LLVMContextRef C);
LLVMBuilderRef LLVMCreateBuilder(void);
LLVMContextRef LLVMContextCreate();
LLVMContextRef LLVMGetGlobalContext();
LLVMContextRef LLVMGetModuleContext(LLVMModuleRef M);
LLVMContextRef LLVMGetTypeContext(LLVMTypeRef Ty);
LLVMDiagnosticSeverity LLVMGetDiagInfoSeverity(LLVMDiagnosticInfoRef DI);
LLVMDiagnosticHandler LLVMContextGetDiagnosticHandler(LLVMContextRef C);
LLVMDLLStorageClass LLVMGetDLLStorageClass(LLVMValueRef Global);
LLVMIntPredicate LLVMGetICmpPredicate(LLVMValueRef Inst);
LLVMLinkage LLVMGetLinkage(LLVMValueRef Global);
LLVMMemoryBufferRef LLVMCreateMemoryBufferWithMemoryRangeCopy(const char *InputData, size_t InputDataLength, const char *BufferName);
LLVMMemoryBufferRef LLVMCreateMemoryBufferWithMemoryRange(const char *InputData, size_t InputDataLength, const char *BufferName, LLVMBool RequiresNullTerminator);
LLVMMetadataRef  LLVMValueMetadataEntriesGetMetadata(LLVMValueMetadataEntry *Entries, unsigned Index);
LLVMMetadataRef LLVMModuleFlagEntriesGetMetadata(LLVMModuleFlagEntry *Entries, unsigned Index);
LLVMMetadataRef LLVMGetModuleFlag(LLVMModuleRef M, const char *Key, size_t KeyLen);
LLVMMetadataRef LLVMValueAsMetadata(LLVMValueRef Val);
LLVMModuleFlagBehavior  LLVMModuleFlagEntriesGetFlagBehavior(LLVMModuleFlagEntry *Entries, unsigned Index);
LLVMModuleFlagEntry *LLVMCopyModuleFlagsMetadata(LLVMModuleRef M, size_t *Len);
LLVMModuleProviderRef  LLVMCreateModuleProviderForExistingModule(LLVMModuleRef M);
LLVMModuleRef LLVMModuleCreateWithName(const char *ModuleID);
LLVMModuleRef LLVMModuleCreateWithNameInContext(const char *ModuleID, LLVMContextRef C);
LLVMModuleRef LLVMGetGlobalParent(LLVMValueRef Global);
LLVMNamedMDNodeRef LLVMGetFirstNamedMetadata(LLVMModuleRef M);
LLVMNamedMDNodeRef LLVMGetLastNamedMetadata(LLVMModuleRef M);
LLVMNamedMDNodeRef LLVMGetNamedMetadata(LLVMModuleRef M, const char *Name, size_t NameLen);
LLVMNamedMDNodeRef LLVMGetOrInsertNamedMetadata(LLVMModuleRef M, const char *Name, size_t NameLen);
LLVMNamedMDNodeRef LLVMGetNextNamedMetadata(LLVMNamedMDNodeRef NMD);
LLVMNamedMDNodeRef LLVMGetPreviousNamedMetadata(LLVMNamedMDNodeRef NMD);
LLVMOpcode LLVMGetConstOpcode(LLVMValueRef ConstantVal);
LLVMOpcode LLVMGetInstructionOpcode(LLVMValueRef Inst);
LLVMPassManagerRef LLVMCreatePassManager();
LLVMPassManagerRef LLVMCreateFunctionPassManager(LLVMModuleProviderRef P);
LLVMPassManagerRef LLVMCreateFunctionPassManagerForModule(LLVMModuleRef M);
LLVMPassRegistryRef LLVMGetGlobalPassRegistry(void);
LLVMRealPredicate LLVMGetFCmpPredicate(LLVMValueRef Inst);
LLVMThreadLocalMode LLVMGetThreadLocalMode(LLVMValueRef GlobalVar);
LLVMTypeKind LLVMGetTypeKind(LLVMTypeRef Ty);
LLVMTypeRef LLVMInt1TypeInContext(LLVMContextRef C);
LLVMTypeRef LLVMInt8TypeInContext(LLVMContextRef C);
LLVMTypeRef LLVMInt16TypeInContext(LLVMContextRef C);
LLVMTypeRef LLVMInt32TypeInContext(LLVMContextRef C);
LLVMTypeRef LLVMInt64TypeInContext(LLVMContextRef C);
LLVMTypeRef LLVMInt128TypeInContext(LLVMContextRef C);
LLVMTypeRef LLVMHalfTypeInContext(LLVMContextRef C);
LLVMTypeRef LLVMFloatTypeInContext(LLVMContextRef C);
LLVMTypeRef LLVMDoubleTypeInContext(LLVMContextRef C);
LLVMTypeRef LLVMX86FP80TypeInContext(LLVMContextRef C);
LLVMTypeRef LLVMFP128TypeInContext(LLVMContextRef C);
LLVMTypeRef LLVMPPCFP128TypeInContext(LLVMContextRef C);
LLVMTypeRef LLVMX86MMXTypeInContext(LLVMContextRef C);
LLVMTypeRef LLVMVoidTypeInContext(LLVMContextRef C);
LLVMTypeRef LLVMLabelTypeInContext(LLVMContextRef C);
LLVMTypeRef LLVMTokenTypeInContext(LLVMContextRef C);
LLVMTypeRef LLVMMetadataTypeInContext(LLVMContextRef C);
LLVMTypeRef LLVMStructCreateNamed(LLVMContextRef C, const char *Name);
LLVMTypeRef LLVMStructTypeInContext(LLVMContextRef C, LLVMTypeRef *ElementTypes, unsigned ElementCount, LLVMBool Packed);
LLVMTypeRef LLVMIntTypeInContext(LLVMContextRef C, unsigned NumBits);
LLVMTypeRef LLVMIntrinsicGetType(LLVMContextRef Ctx, unsigned ID, LLVMTypeRef *ParamTypes, size_t ParamCount);
LLVMTypeRef LLVMGetTypeByName(LLVMModuleRef M, const char *Name);
LLVMTypeRef LLVMStructType(LLVMTypeRef *ElementTypes, unsigned ElementCount, LLVMBool Packed);
LLVMTypeRef LLVMPointerType(LLVMTypeRef ElementType, unsigned AddressSpace);
LLVMTypeRef LLVMArrayType(LLVMTypeRef ElementType, unsigned ElementCount);
LLVMTypeRef LLVMVectorType(LLVMTypeRef ElementType, unsigned ElementCount);
LLVMTypeRef LLVMGetReturnType(LLVMTypeRef FunctionTy);
LLVMTypeRef LLVMFunctionType(LLVMTypeRef ReturnType, LLVMTypeRef *ParamTypes, unsigned ParamCount, LLVMBool IsVarArg);
LLVMTypeRef LLVMStructGetTypeAtIndex(LLVMTypeRef StructTy, unsigned i);
LLVMTypeRef LLVMGetElementType(LLVMTypeRef WrappedTy);
LLVMTypeRef LLVMGetAllocatedType(LLVMValueRef Alloca);
LLVMTypeRef LLVMGlobalGetValueType(LLVMValueRef Global);
LLVMTypeRef LLVMGetCalledFunctionType(LLVMValueRef Instr);
LLVMTypeRef LLVMTypeOf(LLVMValueRef Val);
LLVMTypeRef LLVMIntType(unsigned NumBits);
LLVMTypeRef LLVMInt1Type(void);
LLVMTypeRef LLVMInt8Type(void);
LLVMTypeRef LLVMInt16Type(void);
LLVMTypeRef LLVMInt32Type(void);
LLVMTypeRef LLVMInt64Type(void);
LLVMTypeRef LLVMInt128Type(void);
LLVMTypeRef LLVMHalfType(void);
LLVMTypeRef LLVMFloatType(void);
LLVMTypeRef LLVMDoubleType(void);
LLVMTypeRef LLVMX86FP80Type(void);
LLVMTypeRef LLVMFP128Type(void);
LLVMTypeRef LLVMPPCFP128Type(void);
LLVMTypeRef LLVMX86MMXType(void);
LLVMTypeRef LLVMVoidType(void);
LLVMTypeRef LLVMLabelType(void);
LLVMUnnamedAddr LLVMGetUnnamedAddress(LLVMValueRef Global);
LLVMUseRef LLVMGetNextUse(LLVMUseRef U);
LLVMUseRef LLVMGetFirstUse(LLVMValueRef Val);
LLVMUseRef LLVMGetOperandUse(LLVMValueRef Val, unsigned Index);
LLVMValueKind LLVMGetValueKind(LLVMValueRef Val);
LLVMValueMetadataEntry *  LLVMInstructionGetAllMetadataOtherThanDebugLoc(LLVMValueRef Value, size_t *NumEntries);
LLVMValueMetadataEntry *LLVMGlobalCopyAllMetadata(LLVMValueRef Value, size_t *NumEntries);
LLVMValueRef LLVMConstString(const char *Str, unsigned Length, LLVMBool DontNullTerminate);
LLVMValueRef LLVMMDString(const char *Str, unsigned SLen);
LLVMValueRef LLVMBasicBlockAsValue(LLVMBasicBlockRef BB);
LLVMValueRef LLVMGetBasicBlockParent(LLVMBasicBlockRef BB);
LLVMValueRef LLVMGetBasicBlockTerminator(LLVMBasicBlockRef BB);
LLVMValueRef LLVMGetFirstInstruction(LLVMBasicBlockRef BB);
LLVMValueRef LLVMGetLastInstruction(LLVMBasicBlockRef BB);
LLVMValueRef LLVMBuildRetVoid(LLVMBuilderRef B);
LLVMValueRef LLVMBuildUnreachable(LLVMBuilderRef B);
LLVMValueRef LLVMBuildGlobalString(LLVMBuilderRef B, const char *Str, const char *Name);
LLVMValueRef LLVMBuildGlobalStringPtr(LLVMBuilderRef B, const char *Str, const char *Name);
LLVMValueRef LLVMBuildFence(LLVMBuilderRef B, LLVMAtomicOrdering Ordering, LLVMBool isSingleThread, const char *Name);
LLVMValueRef LLVMBuildBr(LLVMBuilderRef B, LLVMBasicBlockRef Dest);
LLVMValueRef LLVMBuildICmp(LLVMBuilderRef B, LLVMIntPredicate Op, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildBinOp(LLVMBuilderRef B, LLVMOpcode Op, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildCast(LLVMBuilderRef B, LLVMOpcode Op, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildFCmp(LLVMBuilderRef B, LLVMRealPredicate Op, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildMalloc(LLVMBuilderRef B, LLVMTypeRef Ty, const char *Name);
LLVMValueRef LLVMBuildAlloca(LLVMBuilderRef B, LLVMTypeRef Ty, const char *Name);
LLVMValueRef LLVMBuildPhi(LLVMBuilderRef B, LLVMTypeRef Ty, const char *Name);
LLVMValueRef LLVMBuildCall2(LLVMBuilderRef B, LLVMTypeRef Ty, LLVMValueRef Fn, LLVMValueRef *Args, unsigned NumArgs, const char *Name);
LLVMValueRef LLVMBuildInvoke2(LLVMBuilderRef B, LLVMTypeRef Ty, LLVMValueRef Fn, LLVMValueRef *Args, unsigned NumArgs, LLVMBasicBlockRef Then, LLVMBasicBlockRef Catch, const char *Name);
LLVMValueRef LLVMBuildLandingPad(LLVMBuilderRef B, LLVMTypeRef Ty, LLVMValueRef PersFn, unsigned NumClauses, const char *Name);
LLVMValueRef LLVMBuildGEP2(LLVMBuilderRef B, LLVMTypeRef Ty, LLVMValueRef Pointer, LLVMValueRef *Indices, unsigned NumIndices, const char *Name);
LLVMValueRef LLVMBuildInBoundsGEP2(LLVMBuilderRef B, LLVMTypeRef Ty, LLVMValueRef Pointer, LLVMValueRef *Indices, unsigned NumIndices, const char *Name);
LLVMValueRef LLVMBuildStructGEP2(LLVMBuilderRef B, LLVMTypeRef Ty, LLVMValueRef Pointer, unsigned Idx, const char *Name);
LLVMValueRef LLVMBuildLoad2(LLVMBuilderRef B, LLVMTypeRef Ty, LLVMValueRef PointerVal, const char *Name);
LLVMValueRef LLVMBuildArrayMalloc(LLVMBuilderRef B, LLVMTypeRef Ty, LLVMValueRef Val, const char *Name);
LLVMValueRef LLVMBuildArrayAlloca(LLVMBuilderRef B, LLVMTypeRef Ty, LLVMValueRef Val, const char *Name);
LLVMValueRef LLVMBuildAggregateRet(LLVMBuilderRef B, LLVMValueRef *RetVals, unsigned N);
LLVMValueRef LLVMBuildIndirectBr(LLVMBuilderRef B, LLVMValueRef Addr, unsigned NumDests);
LLVMValueRef LLVMBuildInsertValue(LLVMBuilderRef B, LLVMValueRef AggVal, LLVMValueRef EltVal, unsigned Index, const char *Name);
LLVMValueRef LLVMBuildExtractValue(LLVMBuilderRef B, LLVMValueRef AggVal, unsigned Index, const char *Name);
LLVMValueRef LLVMBuildCatchRet(LLVMBuilderRef B, LLVMValueRef CatchPad, LLVMBasicBlockRef BB);
LLVMValueRef LLVMBuildCleanupRet(LLVMBuilderRef B, LLVMValueRef CatchPad, LLVMBasicBlockRef BB);
LLVMValueRef LLVMBuildMemCpy(LLVMBuilderRef B, LLVMValueRef Dst, unsigned DstAlign, LLVMValueRef Src, unsigned SrcAlign, LLVMValueRef Size);
LLVMValueRef LLVMBuildMemMove(LLVMBuilderRef B, LLVMValueRef Dst, unsigned DstAlign, LLVMValueRef Src, unsigned SrcAlign, LLVMValueRef Size);
LLVMValueRef LLVMBuildResume(LLVMBuilderRef B, LLVMValueRef Exn);
LLVMValueRef LLVMBuildCall(LLVMBuilderRef B, LLVMValueRef Fn, LLVMValueRef *Args, unsigned NumArgs, const char *Name);
LLVMValueRef LLVMBuildInvoke(LLVMBuilderRef B, LLVMValueRef Fn, LLVMValueRef *Args, unsigned NumArgs, LLVMBasicBlockRef Then, LLVMBasicBlockRef Catch, const char *Name);
LLVMValueRef LLVMBuildCondBr(LLVMBuilderRef B, LLVMValueRef If, LLVMBasicBlockRef Then, LLVMBasicBlockRef Else);
LLVMValueRef LLVMBuildSelect(LLVMBuilderRef B, LLVMValueRef If, LLVMValueRef Then, LLVMValueRef Else, const char *Name);
LLVMValueRef LLVMBuildAdd(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildNSWAdd(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildNUWAdd(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildFAdd(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildSub(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildNSWSub(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildNUWSub(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildFSub(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildMul(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildNSWMul(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildNUWMul(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildFMul(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildUDiv(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildExactUDiv(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildSDiv(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildExactSDiv(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildFDiv(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildURem(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildSRem(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildFRem(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildShl(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildLShr(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildAShr(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildAnd(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildOr(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildXor(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildPtrDiff(LLVMBuilderRef B, LLVMValueRef LHS, LLVMValueRef RHS, const char *Name);
LLVMValueRef LLVMBuildVAArg(LLVMBuilderRef B, LLVMValueRef List, LLVMTypeRef Ty, const char *Name);
LLVMValueRef LLVMBuildCatchSwitch(LLVMBuilderRef B, LLVMValueRef ParentPad, LLVMBasicBlockRef UnwindBB, unsigned NumHandlers, const char *Name);
LLVMValueRef LLVMBuildCatchPad(LLVMBuilderRef B, LLVMValueRef ParentPad, LLVMValueRef *Args, unsigned NumArgs, const char *Name);
LLVMValueRef LLVMBuildCleanupPad(LLVMBuilderRef B, LLVMValueRef ParentPad, LLVMValueRef *Args, unsigned NumArgs, const char *Name);
LLVMValueRef LLVMBuildGEP(LLVMBuilderRef B, LLVMValueRef Pointer, LLVMValueRef *Indices, unsigned NumIndices, const char *Name);
LLVMValueRef LLVMBuildInBoundsGEP(LLVMBuilderRef B, LLVMValueRef Pointer, LLVMValueRef *Indices, unsigned NumIndices, const char *Name);
LLVMValueRef LLVMBuildStructGEP(LLVMBuilderRef B, LLVMValueRef Pointer, unsigned Idx, const char *Name);
LLVMValueRef LLVMBuildFree(LLVMBuilderRef B, LLVMValueRef PointerVal);
LLVMValueRef LLVMBuildLoad(LLVMBuilderRef B, LLVMValueRef PointerVal, const char *Name);
LLVMValueRef LLVMBuildAtomicCmpXchg(LLVMBuilderRef B, LLVMValueRef Ptr, LLVMValueRef Cmp, LLVMValueRef New, LLVMAtomicOrdering SuccessOrdering, LLVMAtomicOrdering FailureOrdering, LLVMBool singleThread);
LLVMValueRef LLVMBuildMemSet(LLVMBuilderRef B, LLVMValueRef Ptr, LLVMValueRef Val, LLVMValueRef Len, unsigned Align);
LLVMValueRef LLVMBuildRet(LLVMBuilderRef B, LLVMValueRef V);
LLVMValueRef LLVMBuildNeg(LLVMBuilderRef B, LLVMValueRef V, const char *Name);
LLVMValueRef LLVMBuildNSWNeg(LLVMBuilderRef B, LLVMValueRef V, const char *Name);
LLVMValueRef LLVMBuildNUWNeg(LLVMBuilderRef B, LLVMValueRef V, const char *Name);
LLVMValueRef LLVMBuildFNeg(LLVMBuilderRef B, LLVMValueRef V, const char *Name);
LLVMValueRef LLVMBuildNot(LLVMBuilderRef B, LLVMValueRef V, const char *Name);
LLVMValueRef LLVMBuildSwitch(LLVMBuilderRef B, LLVMValueRef V, LLVMBasicBlockRef Else, unsigned NumCases);
LLVMValueRef LLVMBuildShuffleVector(LLVMBuilderRef B, LLVMValueRef V1, LLVMValueRef V2, LLVMValueRef Mask, const char *Name);
LLVMValueRef LLVMBuildIsNull(LLVMBuilderRef B, LLVMValueRef Val, const char *Name);
LLVMValueRef LLVMBuildIsNotNull(LLVMBuilderRef B, LLVMValueRef Val, const char *Name);
LLVMValueRef LLVMBuildTrunc(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildZExt(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildSExt(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildFPToUI(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildFPToSI(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildUIToFP(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildSIToFP(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildFPTrunc(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildFPExt(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildPtrToInt(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildIntToPtr(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildBitCast(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildAddrSpaceCast(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildZExtOrBitCast(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildSExtOrBitCast(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildTruncOrBitCast(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildPointerCast(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildIntCast(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildFPCast(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, const char *Name);
LLVMValueRef LLVMBuildIntCast2(LLVMBuilderRef B, LLVMValueRef Val, LLVMTypeRef DestTy, LLVMBool IsSigned, const char *Name);
LLVMValueRef LLVMBuildStore(LLVMBuilderRef B, LLVMValueRef Val, LLVMValueRef PointerVal);
LLVMValueRef LLVMBuildInsertElement(LLVMBuilderRef B, LLVMValueRef VecVal, LLVMValueRef EltVal, LLVMValueRef Index, const char *Name);
LLVMValueRef LLVMBuildExtractElement(LLVMBuilderRef B, LLVMValueRef VecVal, LLVMValueRef Index, const char *Name);
LLVMValueRef LLVMBuildAtomicRMW(LLVMBuilderRef B,LLVMAtomicRMWBinOp op, LLVMValueRef PTR, LLVMValueRef Val, LLVMAtomicOrdering ordering, LLVMBool singleThread);
LLVMValueRef LLVMGetCurrentDebugLocation(LLVMBuilderRef Builder);
LLVMValueRef LLVMConstStringInContext(LLVMContextRef C, const char *Str, unsigned Length, LLVMBool DontNullTerminate);
LLVMValueRef LLVMMDStringInContext(LLVMContextRef C, const char *Str, unsigned SLen);
LLVMValueRef LLVMMetadataAsValue(LLVMContextRef C, LLVMMetadataRef MD);
LLVMValueRef LLVMConstStructInContext(LLVMContextRef C, LLVMValueRef *ConstantVals, unsigned Count, LLVMBool Packed);
LLVMValueRef LLVMMDNodeInContext(LLVMContextRef C, LLVMValueRef *Vals, unsigned Count);
LLVMValueRef LLVMConstICmp(LLVMIntPredicate Predicate, LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMGetFirstGlobal(LLVMModuleRef M);
LLVMValueRef LLVMGetLastGlobal(LLVMModuleRef M);
LLVMValueRef LLVMGetFirstGlobalAlias(LLVMModuleRef M);
LLVMValueRef LLVMGetLastGlobalAlias(LLVMModuleRef M);
LLVMValueRef LLVMGetFirstFunction(LLVMModuleRef M);
LLVMValueRef LLVMGetLastFunction(LLVMModuleRef M);
LLVMValueRef LLVMGetNamedGlobal(LLVMModuleRef M, const char *Name);
LLVMValueRef LLVMGetNamedFunction(LLVMModuleRef M, const char *Name);
LLVMValueRef LLVMAddFunction(LLVMModuleRef M, const char *Name, LLVMTypeRef FunctionTy);
LLVMValueRef LLVMGetNamedGlobalAlias(LLVMModuleRef M, const char *Name, size_t NameLen);
LLVMValueRef LLVMAddGlobal(LLVMModuleRef M, LLVMTypeRef Ty, const char *Name);
LLVMValueRef LLVMAddGlobalInAddressSpace(LLVMModuleRef M, LLVMTypeRef Ty, const char *Name, unsigned AddressSpace);
LLVMValueRef LLVMAddAlias(LLVMModuleRef M, LLVMTypeRef Ty, LLVMValueRef Aliasee, const char *Name);
LLVMValueRef LLVMGetIntrinsicDeclaration(LLVMModuleRef Mod, unsigned ID, LLVMTypeRef *ParamTypes, size_t ParamCount);
LLVMValueRef LLVMConstFCmp(LLVMRealPredicate Predicate, LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstArray(LLVMTypeRef ElementTy, LLVMValueRef *ConstantVals, unsigned Length);
LLVMValueRef LLVMConstIntOfString(LLVMTypeRef IntTy, const char Str[], uint8_t Radix);
LLVMValueRef LLVMConstIntOfStringAndSize(LLVMTypeRef IntTy, const char Str[], unsigned SLen, uint8_t Radix);
LLVMValueRef LLVMConstInt(LLVMTypeRef IntTy, unsigned long long N, LLVMBool SignExtend);
LLVMValueRef LLVMConstIntOfArbitraryPrecision(LLVMTypeRef IntTy, unsigned NumWords, const uint64_t Words[]);
LLVMValueRef LLVMConstRealOfString(LLVMTypeRef RealTy, const char *Text);
LLVMValueRef LLVMConstRealOfStringAndSize(LLVMTypeRef RealTy, const char Str[], unsigned SLen);
LLVMValueRef LLVMConstReal(LLVMTypeRef RealTy, double N);
LLVMValueRef LLVMConstNamedStruct(LLVMTypeRef StructTy, LLVMValueRef *ConstantVals, unsigned Count);
LLVMValueRef LLVMConstNull(LLVMTypeRef Ty);
LLVMValueRef LLVMConstAllOnes(LLVMTypeRef Ty);
LLVMValueRef LLVMGetUndef(LLVMTypeRef Ty);
LLVMValueRef LLVMConstPointerNull(LLVMTypeRef Ty);
LLVMValueRef LLVMAlignOf(LLVMTypeRef Ty);
LLVMValueRef LLVMSizeOf(LLVMTypeRef Ty);
LLVMValueRef LLVMConstInlineAsm(LLVMTypeRef Ty, const char *AsmString, const char *Constraints, LLVMBool HasSideEffects, LLVMBool IsAlignStack);
LLVMValueRef LLVMGetInlineAsm(LLVMTypeRef Ty, char *AsmString, size_t AsmStringSize, char *Constraints, size_t ConstraintsSize, LLVMBool HasSideEffects, LLVMBool IsAlignStack, LLVMInlineAsmDialect Dialect);
LLVMValueRef LLVMGetUser(LLVMUseRef U);
LLVMValueRef LLVMGetUsedValue(LLVMUseRef U);
LLVMValueRef LLVMConstStruct(LLVMValueRef *ConstantVals, unsigned Count, LLVMBool Packed);
LLVMValueRef LLVMConstVector(LLVMValueRef *ScalarConstantVals, unsigned Size);
LLVMValueRef LLVMMDNode(LLVMValueRef *Vals, unsigned Count);
LLVMValueRef LLVMConstInsertValue(LLVMValueRef AggConstant, LLVMValueRef ElementValueConstant, unsigned *IdxList, unsigned NumIdx);
LLVMValueRef LLVMConstExtractValue(LLVMValueRef AggConstant, unsigned *IdxList, unsigned NumIdx);
LLVMValueRef LLVMAliasGetAliasee(LLVMValueRef Alias);
LLVMValueRef LLVMGetNextParam(LLVMValueRef Arg);
LLVMValueRef LLVMGetPreviousParam(LLVMValueRef Arg);
LLVMValueRef LLVMGetCondition(LLVMValueRef Branch);
LLVMValueRef LLVMGetElementAsConstant(LLVMValueRef C, unsigned idx);
LLVMValueRef LLVMGetParentCatchSwitch(LLVMValueRef CatchPad);
LLVMValueRef LLVMConstSelect(LLVMValueRef ConstantCondition, LLVMValueRef ConstantIfTrue, LLVMValueRef ConstantIfFalse);
LLVMValueRef LLVMConstNeg(LLVMValueRef ConstantVal);
LLVMValueRef LLVMConstNSWNeg(LLVMValueRef ConstantVal);
LLVMValueRef LLVMConstNUWNeg(LLVMValueRef ConstantVal);
LLVMValueRef LLVMConstFNeg(LLVMValueRef ConstantVal);
LLVMValueRef LLVMConstNot(LLVMValueRef ConstantVal);
LLVMValueRef LLVMConstTrunc(LLVMValueRef ConstantVal, LLVMTypeRef ToType);
LLVMValueRef LLVMConstSExt(LLVMValueRef ConstantVal, LLVMTypeRef ToType);
LLVMValueRef LLVMConstZExt(LLVMValueRef ConstantVal, LLVMTypeRef ToType);
LLVMValueRef LLVMConstFPTrunc(LLVMValueRef ConstantVal, LLVMTypeRef ToType);
LLVMValueRef LLVMConstFPExt(LLVMValueRef ConstantVal, LLVMTypeRef ToType);
LLVMValueRef LLVMConstUIToFP(LLVMValueRef ConstantVal, LLVMTypeRef ToType);
LLVMValueRef LLVMConstSIToFP(LLVMValueRef ConstantVal, LLVMTypeRef ToType);
LLVMValueRef LLVMConstFPToUI(LLVMValueRef ConstantVal, LLVMTypeRef ToType);
LLVMValueRef LLVMConstFPToSI(LLVMValueRef ConstantVal, LLVMTypeRef ToType);
LLVMValueRef LLVMConstPtrToInt(LLVMValueRef ConstantVal, LLVMTypeRef ToType);
LLVMValueRef LLVMConstIntToPtr(LLVMValueRef ConstantVal, LLVMTypeRef ToType);
LLVMValueRef LLVMConstBitCast(LLVMValueRef ConstantVal, LLVMTypeRef ToType);
LLVMValueRef LLVMConstAddrSpaceCast(LLVMValueRef ConstantVal, LLVMTypeRef ToType);
LLVMValueRef LLVMConstZExtOrBitCast(LLVMValueRef ConstantVal, LLVMTypeRef ToType);
LLVMValueRef LLVMConstSExtOrBitCast(LLVMValueRef ConstantVal, LLVMTypeRef ToType);
LLVMValueRef LLVMConstTruncOrBitCast(LLVMValueRef ConstantVal, LLVMTypeRef ToType);
LLVMValueRef LLVMConstPointerCast(LLVMValueRef ConstantVal, LLVMTypeRef ToType);
LLVMValueRef LLVMConstFPCast(LLVMValueRef ConstantVal, LLVMTypeRef ToType);
LLVMValueRef LLVMConstIntCast(LLVMValueRef ConstantVal, LLVMTypeRef ToType, LLVMBool isSigned);
LLVMValueRef LLVMConstGEP(LLVMValueRef ConstantVal, LLVMValueRef *ConstantIndices, unsigned NumIndices);
LLVMValueRef LLVMConstInBoundsGEP(LLVMValueRef ConstantVal, LLVMValueRef *ConstantIndices, unsigned NumIndices);
LLVMValueRef LLVMBlockAddress(LLVMValueRef F, LLVMBasicBlockRef BB);
LLVMValueRef LLVMGetNextFunction(LLVMValueRef Fn);
LLVMValueRef LLVMGetPreviousFunction(LLVMValueRef Fn);
LLVMValueRef LLVMGetPersonalityFn(LLVMValueRef Fn);
LLVMValueRef LLVMGetFirstParam(LLVMValueRef Fn);
LLVMValueRef LLVMGetLastParam(LLVMValueRef Fn);
LLVMValueRef LLVMGetParam(LLVMValueRef FnRef, unsigned index);
LLVMValueRef LLVMGetArgOperand(LLVMValueRef Funclet, unsigned i);
LLVMValueRef LLVMGetNextGlobalAlias(LLVMValueRef GA);
LLVMValueRef LLVMGetPreviousGlobalAlias(LLVMValueRef GA);
LLVMValueRef LLVMGetNextGlobal(LLVMValueRef GlobalVar);
LLVMValueRef LLVMGetPreviousGlobal(LLVMValueRef GlobalVar);
LLVMValueRef LLVMGetInitializer(LLVMValueRef GlobalVar);
LLVMValueRef LLVMGetNextInstruction(LLVMValueRef Inst);
LLVMValueRef LLVMGetPreviousInstruction(LLVMValueRef Inst);
LLVMValueRef LLVMInstructionClone(LLVMValueRef Inst);
LLVMValueRef LLVMIsATerminatorInst(LLVMValueRef Inst);
LLVMValueRef LLVMGetMetadata(LLVMValueRef Inst, unsigned KindID);
LLVMValueRef LLVMGetCalledValue(LLVMValueRef Instr);
LLVMValueRef LLVMGetClause(LLVMValueRef LandingPad, unsigned Idx);
LLVMValueRef LLVMConstAdd(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstNSWAdd(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstNUWAdd(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstFAdd(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstSub(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstNSWSub(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstNUWSub(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstFSub(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstMul(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstNSWMul(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstNUWMul(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstFMul(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstUDiv(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstExactUDiv(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstSDiv(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstExactSDiv(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstFDiv(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstURem(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstSRem(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstFRem(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstAnd(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstOr(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstXor(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstShl(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstLShr(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMConstAShr(LLVMValueRef LHSConstant, LLVMValueRef RHSConstant);
LLVMValueRef LLVMGetIncomingValue(LLVMValueRef PhiNode, unsigned Index);
LLVMValueRef LLVMGetParamParent(LLVMValueRef V);
LLVMValueRef LLVMIsAMDNode(LLVMValueRef Val);
LLVMValueRef LLVMIsAMDString(LLVMValueRef Val);
LLVMValueRef LLVMGetOperand(LLVMValueRef Val, unsigned Index);
LLVMValueRef LLVMConstShuffleVector(LLVMValueRef VectorAConstant, LLVMValueRef VectorBConstant, LLVMValueRef MaskConstant);
LLVMValueRef LLVMConstInsertElement(LLVMValueRef VectorConstant, LLVMValueRef ElementValueConstant, LLVMValueRef IndexConstant);
LLVMValueRef LLVMConstExtractElement(LLVMValueRef VectorConstant, LLVMValueRef IndexConstant);
LLVMVisibility LLVMGetVisibility(LLVMValueRef Global);
long long LLVMConstIntGetSExtValue(LLVMValueRef ConstantVal);
size_t LLVMGetBufferSize(LLVMMemoryBufferRef MemBuf);
static AtomicOrdering mapFromLLVMOrdering(LLVMAtomicOrdering Ordering);
static int map_from_llvmopcode(LLVMOpcode code);
static LLVMAtomicOrdering mapToLLVMOrdering(AtomicOrdering Ordering);
static LLVMModuleFlagBehavior  map_from_llvmModFlagBehavior(Module::ModFlagBehavior Behavior);
static LLVMOpcode map_to_llvmopcode(int opcode);
static LLVMValueRef getMDNodeOperandImpl(LLVMContext &Context, const MDNode *N, unsigned Index);
static MDNode *extractMDNode(MetadataAsValue *MAV);
uint64_t LLVMGetEnumAttributeValue(LLVMAttributeRef A);
unsigned long long LLVMConstIntGetZExtValue(LLVMValueRef ConstantVal);
unsigned LLVMGetEnumAttributeKindForName(const char *Name, size_t SLen);
unsigned LLVMGetMDKindID(const char *Name, unsigned SLen);
unsigned LLVMGetEnumAttributeKind(LLVMAttributeRef A);
unsigned LLVMGetMDKindIDInContext(LLVMContextRef C, const char *Name, unsigned SLen);
unsigned LLVMGetNamedMetadataNumOperands(LLVMModuleRef M, const char *Name);
unsigned LLVMGetArrayLength(LLVMTypeRef ArrayTy);
unsigned LLVMCountParamTypes(LLVMTypeRef FunctionTy);
unsigned LLVMGetIntTypeWidth(LLVMTypeRef IntegerTy);
unsigned LLVMGetPointerAddressSpace(LLVMTypeRef PointerTy);
unsigned LLVMCountStructElementTypes(LLVMTypeRef StructTy);
unsigned LLVMGetNumContainedTypes(LLVMTypeRef Tp);
unsigned LLVMGetVectorSize(LLVMTypeRef VectorTy);
unsigned LLVMValueMetadataEntriesGetKind(LLVMValueMetadataEntry *Entries, unsigned Index);
unsigned LLVMGetCallSiteAttributeCount(LLVMValueRef C, LLVMAttributeIndex Idx);
unsigned LLVMGetNumHandlers(LLVMValueRef CatchSwitch);
unsigned LLVMGetAttributeCountAtIndex(LLVMValueRef F, LLVMAttributeIndex Idx);
unsigned LLVMGetIntrinsicID(LLVMValueRef Fn);
unsigned LLVMGetFunctionCallConv(LLVMValueRef Fn);
unsigned LLVMCountParams(LLVMValueRef FnRef);
unsigned LLVMCountBasicBlocks(LLVMValueRef FnRef);
unsigned LLVMGetNumIndices(LLVMValueRef Inst);
unsigned LLVMGetNumArgOperands(LLVMValueRef Instr);
unsigned LLVMGetInstructionCallConv(LLVMValueRef Instr);
unsigned LLVMGetNumClauses(LLVMValueRef LandingPad);
unsigned LLVMCountIncoming(LLVMValueRef PhiNode);
unsigned LLVMGetNumSuccessors(LLVMValueRef Term);
unsigned LLVMGetMDNodeNumOperands(LLVMValueRef V);
unsigned LLVMGetAlignment(LLVMValueRef V);
unsigned LLVMGetDebugLocLine(LLVMValueRef Val);
unsigned LLVMGetDebugLocColumn(LLVMValueRef Val);
unsigned LLVMGetLastEnumAttributeKind(void);
void LLVMShutdown();
void LLVMStopMultithreaded();
void LLVMDisposeMessage(char *Message);
void LLVMMoveBasicBlockBefore(LLVMBasicBlockRef BB, LLVMBasicBlockRef MovePos);
void LLVMMoveBasicBlockAfter(LLVMBasicBlockRef BB, LLVMBasicBlockRef MovePos);
void LLVMDeleteBasicBlock(LLVMBasicBlockRef BBRef);
void LLVMRemoveBasicBlockFromParent(LLVMBasicBlockRef BBRef);
void LLVMClearInsertionPosition(LLVMBuilderRef Builder);
void LLVMDisposeBuilder(LLVMBuilderRef Builder);
void LLVMPositionBuilderAtEnd(LLVMBuilderRef Builder, LLVMBasicBlockRef Block);
void LLVMPositionBuilder(LLVMBuilderRef Builder, LLVMBasicBlockRef Block, LLVMValueRef Instr);
void LLVMSetInstDebugLocation(LLVMBuilderRef Builder, LLVMValueRef Inst);
void LLVMPositionBuilderBefore(LLVMBuilderRef Builder, LLVMValueRef Instr);
void LLVMInsertIntoBuilder(LLVMBuilderRef Builder, LLVMValueRef Instr);
void LLVMInsertIntoBuilderWithName(LLVMBuilderRef Builder, LLVMValueRef Instr, const char *Name);
void LLVMSetCurrentDebugLocation(LLVMBuilderRef Builder, LLVMValueRef L);
void *LLVMContextGetDiagnosticContext(LLVMContextRef C);
void LLVMContextDispose(LLVMContextRef C);
void LLVMContextSetDiscardValueNames(LLVMContextRef C, LLVMBool Discard);
void LLVMContextSetDiagnosticHandler(LLVMContextRef C, LLVMDiagnosticHandler Handler, void *DiagnosticContext);
void LLVMContextSetYieldCallback(LLVMContextRef C, LLVMYieldCallback Callback, void *OpaqueHandle);
void LLVMDisposeMemoryBuffer(LLVMMemoryBufferRef MemBuf);
void LLVMDisposeModuleFlagsMetadata(LLVMModuleFlagEntry *Entries);
void LLVMDisposeModuleProvider(LLVMModuleProviderRef MP);
void LLVMDisposeModule(LLVMModuleRef M);
void LLVMDumpModule(LLVMModuleRef M);
void LLVMSetModuleInlineAsm(LLVMModuleRef M, const char *Asm);
void LLVMSetModuleInlineAsm2(LLVMModuleRef M, const char *Asm, size_t Len);
void LLVMAppendModuleInlineAsm(LLVMModuleRef M, const char *Asm, size_t Len);
void LLVMSetDataLayout(LLVMModuleRef M, const char *DataLayoutStr);
void LLVMSetModuleIdentifier(LLVMModuleRef M, const char *Ident, size_t Len);
void LLVMGetNamedMetadataOperands(LLVMModuleRef M, const char *Name, LLVMValueRef *Dest);
void LLVMAddNamedMetadataOperand(LLVMModuleRef M, const char *Name, LLVMValueRef Val);
void LLVMSetSourceFileName(LLVMModuleRef M, const char *Name, size_t Len);
void LLVMSetTarget(LLVMModuleRef M, const char *Triple);
void LLVMAddModuleFlag(LLVMModuleRef M, LLVMModuleFlagBehavior Behavior, const char *Key, size_t KeyLen, LLVMMetadataRef Val);
void LLVMDisposePassManager(LLVMPassManagerRef PM);
void LLVMInitializeCore(LLVMPassRegistryRef R);
void LLVMGetParamTypes(LLVMTypeRef FunctionTy, LLVMTypeRef *Dest);
void LLVMGetStructElementTypes(LLVMTypeRef StructTy, LLVMTypeRef *Dest);
void LLVMStructSetBody(LLVMTypeRef StructTy, LLVMTypeRef *ElementTypes, unsigned ElementCount, LLVMBool Packed);
void LLVMGetSubtypes(LLVMTypeRef Tp, LLVMTypeRef *Arr);
void LLVMDumpType(LLVMTypeRef Ty);
void LLVMDisposeValueMetadataEntries(LLVMValueMetadataEntry *Entries);
void LLVMAliasSetAliasee(LLVMValueRef Alias, LLVMValueRef Aliasee);
void LLVMSetParamAlignment(LLVMValueRef Arg, unsigned align);
void LLVMSetAtomicSingleThread(LLVMValueRef AtomicInst, LLVMBool NewValue);
void LLVMSetCondition(LLVMValueRef Branch, LLVMValueRef Cond);
void LLVMRemoveCallSiteStringAttribute(LLVMValueRef C, LLVMAttributeIndex Idx, const char *K, unsigned KLen);
void LLVMGetCallSiteAttributes(LLVMValueRef C, LLVMAttributeIndex Idx, LLVMAttributeRef *Attrs);
void LLVMAddCallSiteAttribute(LLVMValueRef C, LLVMAttributeIndex Idx, LLVMAttributeRef A);
void LLVMRemoveCallSiteEnumAttribute(LLVMValueRef C, LLVMAttributeIndex Idx, unsigned KindID);
void LLVMSetTailCall(LLVMValueRef Call, LLVMBool isTailCall);
void LLVMSetParentCatchSwitch(LLVMValueRef CatchPad, LLVMValueRef CatchSwitch);
void LLVMGetHandlers(LLVMValueRef CatchSwitch, LLVMBasicBlockRef *Handlers);
void LLVMAddHandler(LLVMValueRef CatchSwitch, LLVMBasicBlockRef Dest);
void LLVMSetCmpXchgSuccessOrdering(LLVMValueRef CmpXchgInst, LLVMAtomicOrdering Ordering);
void LLVMSetCmpXchgFailureOrdering(LLVMValueRef CmpXchgInst, LLVMAtomicOrdering Ordering);
void LLVMRemoveStringAttributeAtIndex(LLVMValueRef F, LLVMAttributeIndex Idx, const char *K, unsigned KLen);
void LLVMGetAttributesAtIndex(LLVMValueRef F, LLVMAttributeIndex Idx, LLVMAttributeRef *Attrs);
void LLVMAddAttributeAtIndex(LLVMValueRef F, LLVMAttributeIndex Idx, LLVMAttributeRef A);
void LLVMRemoveEnumAttributeAtIndex(LLVMValueRef F, LLVMAttributeIndex Idx, unsigned KindID);
void LLVMDeleteFunction(LLVMValueRef Fn);
void LLVMAddTargetDependentFunctionAttr(LLVMValueRef Fn, const char *A, const char *V);
void LLVMSetGC(LLVMValueRef Fn, const char *GC);
void LLVMSetPersonalityFn(LLVMValueRef Fn, LLVMValueRef PersonalityFn);
void LLVMSetFunctionCallConv(LLVMValueRef Fn, unsigned CC);
void LLVMGetBasicBlocks(LLVMValueRef FnRef, LLVMBasicBlockRef *BasicBlocksRefs);
void LLVMGetParams(LLVMValueRef FnRef, LLVMValueRef *ParamRefs);
void LLVMSetArgOperand(LLVMValueRef Funclet, unsigned i, LLVMValueRef value);
void LLVMSetIsInBounds(LLVMValueRef GEP, LLVMBool InBounds);
void LLVMGlobalClearMetadata(LLVMValueRef Global);
void LLVMSetSection(LLVMValueRef Global, const char *Section);
void LLVMSetUnnamedAddr(LLVMValueRef Global, LLVMBool HasUnnamedAddr);
void LLVMSetDLLStorageClass(LLVMValueRef Global, LLVMDLLStorageClass Class);
void LLVMSetLinkage(LLVMValueRef Global, LLVMLinkage Linkage);
void LLVMSetUnnamedAddress(LLVMValueRef Global, LLVMUnnamedAddr UnnamedAddr);
void LLVMSetVisibility(LLVMValueRef Global, LLVMVisibility Viz);
void LLVMGlobalEraseMetadata(LLVMValueRef Global, unsigned Kind);
void LLVMGlobalSetMetadata(LLVMValueRef Global, unsigned Kind, LLVMMetadataRef MD);
void LLVMDeleteGlobal(LLVMValueRef GlobalVar);
void LLVMSetGlobalConstant(LLVMValueRef GlobalVar, LLVMBool IsConstant);
void LLVMSetExternallyInitialized(LLVMValueRef GlobalVar, LLVMBool IsExtInit);
void LLVMSetThreadLocal(LLVMValueRef GlobalVar, LLVMBool IsThreadLocal);
void LLVMSetThreadLocalMode(LLVMValueRef GlobalVar, LLVMThreadLocalMode Mode);
void LLVMSetInitializer(LLVMValueRef GlobalVar, LLVMValueRef ConstantVal);
void LLVMAddDestination(LLVMValueRef IndirectBr, LLVMBasicBlockRef Dest);
void LLVMInstructionRemoveFromParent(LLVMValueRef Inst);
void LLVMInstructionEraseFromParent(LLVMValueRef Inst);
void LLVMSetMetadata(LLVMValueRef Inst, unsigned KindID, LLVMValueRef Val);
void LLVMSetInstructionCallConv(LLVMValueRef Instr, unsigned CC);
void LLVMSetInstrParamAlignment(LLVMValueRef Instr, unsigned index, unsigned align);
void LLVMSetNormalDest(LLVMValueRef Invoke, LLVMBasicBlockRef B);
void LLVMSetUnwindDest(LLVMValueRef Invoke, LLVMBasicBlockRef B);
void LLVMSetCleanup(LLVMValueRef LandingPad, LLVMBool Val);
void LLVMAddClause(LLVMValueRef LandingPad, LLVMValueRef ClauseVal);
void LLVMSetOrdering(LLVMValueRef MemAccessInst, LLVMAtomicOrdering Ordering);
void LLVMSetVolatile(LLVMValueRef MemAccessInst, LLVMBool isVolatile);
void LLVMReplaceAllUsesWith(LLVMValueRef OldVal, LLVMValueRef NewVal);
void LLVMAddIncoming(LLVMValueRef PhiNode, LLVMValueRef *IncomingValues, LLVMBasicBlockRef *IncomingBlocks, unsigned Count);
void LLVMAddCase(LLVMValueRef Switch, LLVMValueRef OnVal, LLVMBasicBlockRef Dest);
void LLVMSetSuccessor(LLVMValueRef Term, unsigned i, LLVMBasicBlockRef block);
void LLVMGetMDNodeOperands(LLVMValueRef V, LLVMValueRef *Dest);
void LLVMSetAlignment(LLVMValueRef V, unsigned Bytes);
void LLVMDumpValue(LLVMValueRef Val);
void LLVMSetValueName(LLVMValueRef Val, const char *Name);
void LLVMSetValueName2(LLVMValueRef Val, const char *Name, size_t NameLen);
void LLVMSetOperand(LLVMValueRef Val, unsigned Index, LLVMValueRef Op);";