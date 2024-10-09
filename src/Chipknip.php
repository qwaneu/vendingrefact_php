<?php
namespace eu\qwan\vender;

class Chipknip implements PaymentMethodInterface {
    public int $credits;

    public function __construct($initial_value) {
        $this->credits = $initial_value;
    }

    public function hasBalance($amount): bool
    {
        return $this->credits >= $amount;
    }

    public function reduceBalance($amount): void
    {
        $this->credits -= $amount;
    }
}

