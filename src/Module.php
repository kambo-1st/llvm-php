<?php

namespace Kambo\LLVM;

/**
 * A Module instance is used to store all the information related to an LLVM module.
 * Modules are the top level container of all other LLVM Intermediate Representation (IR) objects.
 * Each module directly contains a list of globals variables, a list of functions, a list of libraries
 * (or other modules) this module depends on, a symbol table, and various data about the target's characteristics.
 * A module maintains a GlobalValRefMap object that is used to hold all constant references to global
 * variables in the module. When a global variable is destroyed, it should have no entries in the GlobalValueRefMap.
 * The main container class for the LLVM Intermediate Representation.
 *
 * @author  Bohuslav Simek <bohuslav@simek.si>
 * @license BSD
 */
class Module
{
    public function __construct(string $name)
    {

    }
}
