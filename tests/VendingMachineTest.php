<?php

namespace Tests;

use eu\qwan\vender\App\VendingMachine;
use eu\qwan\vender\Can;
use eu\qwan\vender\Chipknip;
use eu\qwan\vender\Choice;
use PHPUnit\Framework\TestCase;

class VendingMachineTest extends TestCase {
    private VendingMachine $machine;

    protected function setUp(): void {
        $this->machine = new VendingMachine();
    }

    public function testChoiceLessMachineDeliversNothing() {
        $this->assertEquals(Can::NONE, $this->machine->deliver(Choice::COLA));
        $this->assertEquals(Can::NONE, $this->machine->deliver(Choice::FANTA));
    }

    public function testDeliversCanOfChoice() {
        $this->machine->configure(Choice::COLA, Can::COLA, 10);
        $this->machine->configure(Choice::FANTA, Can::FANTA, 10);
        $this->machine->configure(Choice::SPRITE, Can::SPRITE, 10);
        $this->assertEquals(Can::COLA, $this->machine->deliver(Choice::COLA));
        $this->assertEquals(Can::FANTA, $this->machine->deliver(Choice::FANTA));
        $this->assertEquals(Can::SPRITE, $this->machine->deliver(Choice::SPRITE));
    }

    public function testDeliversNothingWhenMakingInvalidChoice() {
        $this->machine->configure(Choice::COLA, Can::COLA, 10);
        $this->machine->configure(Choice::FANTA, Can::FANTA, 10);
        $this->machine->configure(Choice::SPRITE, Can::SPRITE, 10);
        $this->assertEquals(Can::NONE, $this->machine->deliver(Choice::BEER));
    }

    public function testDeliversNothingWhenNotPaid() {
        $this->machine->configure(Choice::FANTA, Can::FANTA, 10, 2);
        $this->machine->configure(Choice::SPRITE, Can::SPRITE, 10, 1);

        $this->assertEquals(Can::NONE, $this->machine->deliver(Choice::FANTA));
    }

    public function testDeliversFantaWhenPaid() {
        $this->machine->configure(Choice::SPRITE, Can::SPRITE, 10, 1);
        $this->machine->configure(Choice::FANTA, Can::FANTA, 10, 2);

        $this->machine->set_value(2);
        $this->assertEquals(Can::FANTA, $this->machine->deliver(Choice::FANTA));
        $this->assertEquals(Can::NONE, $this->machine->deliver(Choice::SPRITE));
    }

    public function testDeliversSpriteWhenPaid() {
        $this->machine->configure(Choice::SPRITE, Can::SPRITE, 10, 1);
        $this->machine->configure(Choice::FANTA, Can::FANTA, 10, 2);

        $this->machine->set_value(2);
        $this->assertEquals(Can::SPRITE, $this->machine->deliver(Choice::SPRITE));
        $this->assertEquals(Can::SPRITE, $this->machine->deliver(Choice::SPRITE));
        $this->assertEquals(Can::NONE, $this->machine->deliver(Choice::SPRITE));
    }

    public function testAddPayments() {
        $this->machine->configure(Choice::SPRITE, Can::SPRITE, 10, 1);
        $this->machine->configure(Choice::FANTA, Can::FANTA, 10, 2);

        $this->machine->set_value(1);
        $this->machine->set_value(1);
        $this->assertEquals(Can::SPRITE, $this->machine->deliver(Choice::SPRITE));
        $this->assertEquals(Can::SPRITE, $this->machine->deliver(Choice::SPRITE));
        $this->assertEquals(Can::NONE, $this->machine->deliver(Choice::SPRITE));
    }

    public function testReturnsChange() {
        $this->machine->configure(Choice::SPRITE, Can::SPRITE, 10, 1);
        $this->machine->set_value(2);
        $this->assertEquals(2, $this->machine->get_change());
        $this->assertEquals(0, $this->machine->get_change());
    }

    public function testStock() {
        $this->machine->configure(Choice::SPRITE, Can::SPRITE, 1);
        $this->assertEquals(Can::SPRITE, $this->machine->deliver(Choice::SPRITE));
        $this->assertEquals(Can::NONE, $this->machine->deliver(Choice::SPRITE));
    }

    public function testAddStock() {
        $this->machine->configure(Choice::SPRITE, Can::SPRITE, 1);
        $this->machine->configure(Choice::SPRITE, Can::SPRITE, 1);
        $this->assertEquals(Can::SPRITE, $this->machine->deliver(Choice::SPRITE));
        $this->assertEquals(Can::SPRITE, $this->machine->deliver(Choice::SPRITE));
        $this->assertEquals(Can::NONE, $this->machine->deliver(Choice::SPRITE));
    }

    public function testCheckoutChipIfChipknipInserted() {
        $this->machine->configure(Choice::SPRITE, Can::SPRITE, 1, 1);
        $chip = new Chipknip(10);
        $this->machine->insert_chip($chip);
        $this->assertEquals(Can::SPRITE, $this->machine->deliver(Choice::SPRITE));
        $this->assertEquals(9, $chip->credits);
    }

    public function testCheckoutChipEmpty() {
        $this->machine->configure(Choice::SPRITE, Can::SPRITE, 1, 1);
        $chip = new Chipknip(0);
        $this->machine->insert_chip($chip);
        $this->assertEquals(Can::NONE, $this->machine->deliver(Choice::SPRITE));
        $this->assertEquals(0, $chip->credits);
    }
}
