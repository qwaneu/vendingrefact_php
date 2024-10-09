<?php

namespace eu\qwan\vender;

class MoneyTill implements PaymentMethodInterface
{
    public int $balance = 0;

    public function hasBalance($amount): bool
    {
        return $this->balance >= $amount;
    }

    public function reduceBalance($amount): void
    {
        $this->balance -= $amount;
    }

    public function addBalance($amount)
    {
        $this->balance += $amount;
    }

    public function getBalance()
    {
        return $this->balance;
    }
}