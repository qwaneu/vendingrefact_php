<?php

namespace eu\qwan\vender\App;

use eu\qwan\vender\Can;
use eu\qwan\vender\CanContainer;
use eu\qwan\vender\Chipknip;
use eu\qwan\vender\Choice;

class VendingMachine
{
    private array $cans = array();
    private int $payment_method = 0;
    private Chipknip $chipknip;
    private int $balance = 0;

    public function addBalance($amount): void
    {
        $this->payment_method = 1;
        $this->balance += $amount;
    }

    public function insert_chip($chipknip): void
    {
        // TODO: can't pay with chip in Britain
        $this->payment_method = 2;
        $this->chipknip = $chipknip;
    }

    public function deliver(Choice $choice): Can
    {
        $res = Can::NONE;
        $key = $choice->value;

        $canContainer = $this->getCanContainer($choice);
        if (isset($canContainer)) {
            //
            // step2 : check price
            //
            if ($canContainer->getPrice() == 0) {
                $res = $canContainer->getType();
                // or price matches
            } else {

                switch ($this->payment_method) {
                    case 1: // paying with coins
                        if ($canContainer->getPrice() <= $this->balance) {
                            $res = $canContainer->getType();
                            $this->balance -= $canContainer->getPrice();
                        }
                        break;
                    case 2: // paying with chipknip
                        if ($this->chipknip->HasValue($canContainer->getPrice())) {
                            $this->chipknip->Reduce($canContainer->getPrice());
                            $res = $canContainer->getType();
                        }
                        break;
                    default:
                        // TODO: Is this a valid situation?:
                        // larry forgot the } else { clause
                        // i added it, but i am acutally not sure as to wether this
                        // is a problem
                        // unknown payment
                        break;
                        // i think(i) nobody inserted anything
                }
            }
        } else {
            $res = Can::NONE;
        if (!isset($canContainer)) {
            return Can::NONE;
        }

        //
        // step 3: check stock
        //
        if ($res != Can::NONE) {
            if ($canContainer->getAmount() <= 0) {
                $res = Can::NONE;
            } else {
                $canContainer->setAmount($canContainer->getAmount() - 1);
            }
        }

        // if canContainer is set then return {
        // otherwise we need to return the none
        if ($res == Can::NONE) {
            return Can::NONE;
        }

        return $res;
    }

    public function get_change(): int
    {
        $to_return = 0;
        if ($this->balance > 0) {
            $to_return = $this->balance;
            $this->balance = 0;
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

