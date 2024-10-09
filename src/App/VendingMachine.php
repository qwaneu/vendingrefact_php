<?php

namespace eu\qwan\vender\App;

use eu\qwan\vender\Can;
use eu\qwan\vender\CanContainer;
use eu\qwan\vender\Chipknip;
use eu\qwan\vender\Choice;
use eu\qwan\vender\MoneyTill;
use eu\qwan\vender\PaymentMethodInterface;

/**
 * TODO
 * - Change names
 * - deliver method split-up
 * --> extract check price
 * --> $this->cans[$key] into a variable
 * -->
 */
class VendingMachine
{
    private array $cans = array();
    private ?PaymentMethodInterface $selectPaymentMethod = null;
    private MoneyTill $moneyTill;

    public function __construct()
    {
        $this->moneyTill = new MoneyTill();

        $this->selectPaymentMethod = $this->moneyTill;
    }

    public function addBalance($amount): void
    {
        $this->selectPaymentMethod = $this->moneyTill;
        $this->moneyTill->addBalance($amount);
    }

    public function insertChip($chipknip): void
    {
        // TODO: can't pay with chip in Britain
        $this->selectPaymentMethod = $chipknip;
    }

    public function deliver(Choice $choice): Can
    {
        $canContainer = $this->getCanContainer($choice);
        if (!isset($canContainer) ||
            !$this->selectPaymentMethod->hasBalance($canContainer->getPrice()) ||
            $canContainer->getAmount() <= 0) {
            return Can::NONE;
        }

        $canContainer->setAmount($canContainer->getAmount() - 1);
        $this->selectPaymentMethod->reduceBalance($canContainer->getPrice());

        return $canContainer->getType();
    }

    public function getChange(): int
    {
        $to_return = 0;
        if ($this->moneyTill->getBalance() > 0) {
            $to_return = $this->moneyTill->getBalance();
            $this->moneyTill->reduceBalance($to_return);
        }
        return $to_return;
    }

    public function configure($choice, $c, $n, $price = 0): void
    {
        $key = $choice->value;

        if (array_key_exists($key, $this->cans)) {
            $this->cans[$key]->setAmount($this->cans[$key]->getAmount() + $n);
            return;
        }
        $can = new CanContainer();
        $can->setType($c);
        $can->setAmount($n);
        $can->setPrice($price);
        $this->cans[$key] = $can;
    }

    /**
     * @param Choice $choice
     * @return ?CanContainer
     */
    public function getCanContainer(Choice $choice): ?CanContainer
    {
        return array_key_exists($choice->value, $this->cans) ? $this->cans[$choice->value] : null;
    }
}

