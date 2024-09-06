<?php

use PHPUnit\Framework\TestCase;

class VendingMachineTest extends TestCase
{
    private $vendingMachine;

    protected function setUp(): void
    {
        $this->vendingMachine = new VendingMachine();
    }

    public function testBuyWithExactChange()
    {
        $this->vendingMachine->insert(25);
        $this->vendingMachine->insert(25);

        $this->assertEquals("THANK YOU", $this->vendingMachine->buy());
        $this->assertEquals(50, $this->vendingMachine->getInsertedAmount());
        $this->assertEquals("INSERT COIN", $this->vendingMachine->checkDisplay());
    }

    public function testBuyWithInsufficientChange()
    {
        $this->vendingMachine->insert(25);

        $this->assertEquals("PRICE: $0.50", $this->vendingMachine->buy());
        $this->assertEquals(25, $this->vendingMachine->getInsertedAmount());
        $this->assertEquals("PRICE: $0.50", $this->vendingMachine->checkDisplay());
        $this->assertEquals("INSERT COIN", $this->vendingMachine->checkDisplay());
    }

    public function testReturnCoins()
    {
        $this->vendingMachine->insert(25);
        $this->vendingMachine->insert(10);

        $this->assertEquals(35, $this->vendingMachine->returnCoins());
        $this->assertEquals(0, $this->vendingMachine->getInsertedAmount());
        $this->assertEquals("INSERT COIN", $this->vendingMachine->checkDisplay());
    }

    public function testExactChangeOnly()
    {
        $this->vendingMachine->setExactChangeOnly(true);

        $this->assertEquals("EXACT CHANGE ONLY", $this->vendingMachine->checkDisplay());
    }
}
