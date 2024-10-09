<?php

namespace eu\qwan\vender\App;

use eu\qwan\vender\Can;
use eu\qwan\vender\CanContainer;
use eu\qwan\vender\Chipknip;
use eu\qwan\vender\Choice;
use eu\qwan\vender\MoneyTill;
use eu\qwan\vender\PaymentMethodInterface;

class VendingMachine
{
    private array $cans = array();
    private PaymentMethodInterface $selectPaymentMethod;
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

        if($this->canBuyCan($canContainer)){
            return $this->buyCan($canContainer);
        }

        return Can::NONE;
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

    public function configure($choice, $canType, $amount, $price = 0): void
    {
        $key = $choice->value;

        if (!array_key_exists($key, $this->cans)) {
            $this->cans[$key] = new CanContainer();
        }

        $this->cans[$key]->setType($canType);
        $this->cans[$key]->setPrice($price);
        $this->cans[$key]->setAmount($this->cans[$key]->getAmount() + $amount);
    }

    /**
     * @param Choice $choice
     * @return ?CanContainer
     */
    public function getCanContainer(Choice $choice): ?CanContainer
    {
        return array_key_exists($choice->value, $this->cans) ? $this->cans[$choice->value] : null;
    }

    private function buyCan(?CanContainer $canContainer): Can
    {
        if($canContainer === null) {
            return Can::NONE;
        }

        $canContainer->setAmount($canContainer->getAmount() - 1);
        $this->selectPaymentMethod->reduceBalance($canContainer->getPrice());

        return $canContainer->getType();
    }

    private function canBuyCan(?CanContainer $canContainer): bool
    {
        return isset($canContainer) &&
            $this->hasEnoughBalanceToBuy($canContainer) &&
            $this->hasCansLeft($canContainer);
    }

    /**
     * @param CanContainer $canContainer
     * @return bool
     */
    public function hasCansLeft(CanContainer $canContainer): bool
    {
        return $canContainer->getAmount() > 0;
    }

    /**
     * @param CanContainer $canContainer
     * @return bool
     */
    public function hasEnoughBalanceToBuy(CanContainer $canContainer): bool
    {
        return $this->selectPaymentMethod->hasBalance($canContainer->getPrice());
    }
}

