<?php


use PHPUnit\Framework\TestCase;
use VendingMachine;
use Can;

class VendingMachineGeneratedTest extends TestCase
{
    public function testChoiceless_machine_delivers_nothing() {
        $machine = new VendingMachine();
        $this->assertEquals(Can::None, $machine->deliver('cola'));
        $this->assertEquals(Can::None, $machine->deliver('fanta'));
    }


}
