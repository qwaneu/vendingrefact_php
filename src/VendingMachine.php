<?php

namespace eu\qwan\vender;

class VendingMachine
{
    private array $cans = array();
    private int $payment_method = 0;
    private Chipknip $chipknip;
    private int $c = -1;
    private int $price;

    public function set_value($v): void
    {
        $this->payment_method = 1;
        if ($this->c != -1) {
            $this->c += $v;
        } else {
            $this->c = $v;
        }
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
        $key = $choice->value;  // Cast choice to string to us in array as key

        if (array_key_exists($key, $this->cans)) {
            if ($this->cans[$key]->getPrice() == 0) {
                $res = $this->cans[$key]->getType();
            } else {
                switch ($this->payment_method) {
                    case 1: // paying with coins
                        if ($this->c != -1 && $this->cans[$key]->getPrice() <= $this->c) {
                            $res = $this->cans[$key]->getType();
                            $this->c -= $this->cans[$key]->getPrice();
                        }
                        break;
                    case 2: // paying with chipknip
                        if ($this->chipknip->HasValue($this->cans[$key]->getPrice())) {
                            $this->chipknip->Reduce($this->cans[$key]->getPrice());
                            $res = $this->cans[$key]->getType();
                        }
                        break;
                    default:
                        // unknown payment
                        break;
                }
            }
        } else {
            $res = Can::NONE;
        }

        if ($res != Can::NONE) {
            if ($this->cans[$key]->getAmount() <= 0) {
                $res = Can::NONE;
            } else {
                $this->cans[$key]->setAmount($this->cans[$key]->getAmount() - 1);
            }
        }

        return $res;
    }

    public function get_change(): int
    {
        $to_return = 0;
        if ($this->c > 0) {
            $to_return = $this->c;
            $this->c = 0;
        }
        return $to_return;
    }

    public function configure($choice, $c, $n, $price = 0): void
    {
        $this->price = $price;
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
}

