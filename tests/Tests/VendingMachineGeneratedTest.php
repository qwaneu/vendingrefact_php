<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

enum Can
{
    case Nothing;
}

enum Choice
{
    case Cola;
    case Fanta;
}

class VendingMachine {

    public function deliver(Choice $choice) {
        return Can::Nothing;
}

}

class VendingMachineGeneratedTest extends TestCase
{
    public function testChoiceless_machine_delivers_nothing()
    {
        $machine = new VendingMachine();
        $this->assertEquals(Can::Nothing, $machine->deliver(Choice::Cola));
        $this->assertEquals(Can::Nothing, $machine->deliver(Choice::Fanta));
    }


}
