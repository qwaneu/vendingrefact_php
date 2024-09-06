<?php

require_once 'src/Coin.php';
require_once 'src/Product.php';
require_once 'src/VendingMachine.php';

class VendingMachineTest
{
    private $vendingMachine;

    public function setUp(): void
    {
        $this->vendingMachine = new VendingMachine();
    }

    public function testBuyWithExactChange()
    {
        $this->vendingMachine->insert(new Coin(25, "quarter"));
        $this->vendingMachine->insert(new Coin(25, "quarter"));

        $this->assertEquals(true, $this->vendingMachine->buy("chips"));
        $this->assertEquals(0, $this->vendingMachine->getInsertedAmount());
        $this->assertEquals("INSERT COIN", $this->vendingMachine->checkDisplay());
    }

    public function testBuyWithInsufficientChange()
    {
        $this->vendingMachine->insert(new Coin(25, "quarter"));

        $this->assertEquals(false, $this->vendingMachine->buy("chips"));
        $this->assertEquals(25, $this->vendingMachine->getInsertedAmount());
        $this->assertEquals("PRICE: $0.50", $this->vendingMachine->checkDisplay());
        $this->assertEquals("$0.25", $this->vendingMachine->checkDisplay());
    }

    public function testReturnCoins()
    {
        $this->vendingMachine->insert(new Coin(25, "quarter"));
        $this->vendingMachine->insert(new Coin(10, "dime"));

        $this->assertEquals(35, $this->vendingMachine->returnCoins());
        $this->assertEquals(0, $this->vendingMachine->getInsertedAmount());
        $this->assertEquals("INSERT COIN", $this->vendingMachine->checkDisplay());
    }

    public function testExactChangeOnly()
    {
        $this->vendingMachine->setExactChangeOnly(true);

        $this->assertEquals("EXACT CHANGE ONLY", $this->vendingMachine->checkDisplay());
    }

    public function runTests()
    {
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if (strpos($method, 'test') === 0) {
                $this->setUp();
                $this->$method();
                echo "Test $method passed.\n";
            }
        }
    }

    private function assertEquals($expected, $actual)
    {
        if ($expected !== $actual) {
            throw new Exception("Assertion failed: expected $expected, got $actual");
        }
    }
}

// Run the tests
$test = new VendingMachineTest();
$test->runTests();
