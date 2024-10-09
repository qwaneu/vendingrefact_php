<?php

namespace eu\qwan\vender;

interface PaymentMethodInterface
{
    public function hasBalance($amount): bool;

    public function reduceBalance($amount): void;

}