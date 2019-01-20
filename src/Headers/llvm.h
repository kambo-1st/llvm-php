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
 LLVMValueRef LLVMAddFunction(LLVMModuleRef M, const char *Name, LLVMTypeRef FunctionTy);
 LLVMBasicBlockRef LLVMAppendBasicBlockInContext(LLVMContextRef C, LLVMValueRef FnRef, const char *Name);
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

 /**
  * Verifies that a module is valid, taking the specified action if not.
  * Optionally returns a human-readable description of any invalid constructs.
  * OutMessage must be disposed with LLVMDisposeMessage.
  */
 LLVMBool LLVMVerifyModule(LLVMModuleRef M, LLVMVerifierFailureAction Action, char **OutMessage);

 /**
  * Verifies that a single function is valid, taking the specified action. Useful
  * for debugging.
  */
 LLVMBool LLVMVerifyFunction(LLVMValueRef Fn, LLVMVerifierFailureAction Action);

 /**
  * Open up a ghostview window that displays the CFG of the current function.
  * Useful for debugging.
  */
 void LLVMViewFunctionCFG(LLVMValueRef Fn);
 void LLVMViewFunctionCFGOnly(LLVMValueRef Fn);

 /**
  * Execution engine
  * http://llvm.org/doxygen/group__LLVMCExecutionEngine.html
  */
 typedef struct LLVMOpaqueGenericValue *LLVMGenericValueRef;
 typedef struct LLVMOpaqueExecutionEngine *LLVMExecutionEngineRef;
 typedef struct LLVMOpaqueMCJITMemoryManager *LLVMMCJITMemoryManagerRef;

 void LLVMLinkInMCJIT();
 void LLVMLinkInInterpreter(void);

 LLVMGenericValueRef LLVMRunFunction(
    LLVMExecutionEngineRef EE,
    LLVMValueRef F,
    unsigned NumArgs,
    LLVMGenericValueRef *Args
 );

 LLVMBool LLVMCreateInterpreterForModule(LLVMExecutionEngineRef *OutInterp, LLVMModuleRef M, char **OutError);

 /* Target.h*/

 static inline LLVMBool LLVMInitializeNativeTarget(void);



 /* BitWriter.cpp */
 int LLVMWriteBitcodeToFile(LLVMModuleRef M, const char *Path);

 /* ExecutionEngineBindings.cpp */
 LLVMGenericValueRef LLVMCreateGenericValueOfInt(LLVMTypeRef Ty, unsigned long long N, LLVMBool IsSigned);
 unsigned long long LLVMGenericValueToInt(LLVMGenericValueRef GenValRef, LLVMBool IsSigned);
