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
        if (!isset($canContainer)) {
            return Can::NONE;
        }

        $res = $canContainer->getType();

        //
        // step2 : check price
        //
        if ($canContainer->getPrice() > 0) {
            if($this->selectPaymentMethod?->hasBalance($canContainer->getPrice())) {
                $this->selectPaymentMethod->reduceBalance($canContainer->getPrice());
            } else {
                $res = Can::NONE;
            }
        }

        //
        // step 3: check stock
        //
        if ($canContainer->getAmount() <= 0) {
            $res = Can::NONE;
        } else {
            $canContainer->setAmount($canContainer->getAmount() - 1);
        }

        return $res;
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

