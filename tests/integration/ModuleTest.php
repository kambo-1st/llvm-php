<?php

namespace Kambo\Tests\Yasna\Unit;

use PHPUnit\Framework\TestCase;
use Kambo\LLVM\Module;
use Kambo\LLVM\FunctionType;
use Kambo\LLVM\Type;

/**
 * Unit tests for class Kambo\LLVM\Module
 *
 * @author Bohuslav Simek <bohuslav@simek.si>
 */
class ModuleTest extends TestCase
{
    /**
     * Tests load bytecode
     *
     * @return void
     */
    public function testPrintModuleToString(): void
    {
        $module = new Module('my_module');
        $paramTypes = [
            Type::getInt32Ty(), Type::getInt32Ty(),
        ];
        $functionType = FunctionType::get(Type::getInt32Ty(), $paramTypes,0);


        $this->assertEquals($expectedSections, $bytecode->getSections());
    }
}
