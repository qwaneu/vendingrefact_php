<?php

class VendingMachine {
    private $insertedAmount = 0;
    private $display = "INSERT COIN";
    private $coins = [];
    private $products = [];
    private $exactChangeOnly = false;

    public function __construct() {
        $this->coins[] = new Coin(25, "quarter");
        $this->coins[] = new Coin(10, "dime");
        $this->coins[] = new Coin(5, "nickel");

        $this->products[] = new Product("cola", 100);
        $this->products[] = new Product("chips", 50);
        $this->products[] = new Product("candy", 65);
    }

    public function insert($coin) {
        if ($this->isValidCoin($coin)) {
            $this->insertedAmount += $coin->getValue();
            $this->updateDisplay();
        }
    }

    public function buy($productName) {
        $product = $this->findProduct($productName);
        if ($product !== null) {
            if ($this->insertedAmount >= $product->getPrice()) {
                $this->insertedAmount -= $product->getPrice();
                $this->display = "THANK YOU";
                return true;
            } else {
                $this->display = "PRICE: $" . number_format($product->getPrice() / 100, 2);
            }
        }
        return false;
    }

    public function returnCoins() {
        $returnedAmount = $this->insertedAmount;
        $this->insertedAmount = 0;
        $this->updateDisplay();
        return $returnedAmount;
    }

    public function checkDisplay() {
        $currentDisplay = $this->display;
        if ($this->display !== "INSERT COIN" && $this->display !== "EXACT CHANGE ONLY") {
            $this->updateDisplay();
        }
        return $currentDisplay;
    }

    public function getInsertedAmount() {
        return $this->insertedAmount;
    }

    public function setExactChangeOnly($value) {
        $this->exactChangeOnly = $value;
        $this->updateDisplay();
    }

    private function isValidCoin($coin) {
        foreach ($this->coins as $validCoin) {
            if ($coin->getValue() === $validCoin->getValue()) {
                return true;
            }
        }
        return false;
    }

    private function findProduct($name) {
        foreach ($this->products as $product) {
            if ($product->getName() === $name) {
                return $product;
            }
        }
        return null;
    }

    private function updateDisplay() {
        if ($this->exactChangeOnly) {
            $this->display = "EXACT CHANGE ONLY";
        } elseif ($this->insertedAmount > 0) {
            $this->display = "$" . number_format($this->insertedAmount / 100, 2);
        } else {
            $this->display = "INSERT COIN";
        }
    }
}
