<?php

namespace eu\qwan\vender;

interface PaymentMethodInterface
{
    public function hasBalance(int $amount): bool;

    public function reduceBalance(int $amount): void;

}