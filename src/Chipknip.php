<?php
namespace eu\qwan\vender;

class Chipknip {
    public int $credits;

    public function __construct($initial_value) {
        $this->credits = $initial_value;
    }

    public function hasValue($p): bool
    {
        return $this->credits >= $p;
    }

    public function reduce($p): void
    {
        $this->credits -= $p;
    }
}