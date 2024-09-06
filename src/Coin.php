<?php

class Coin {
    private $value;
    private $name;

    public function __construct($value, $name) {
        $this->value = $value;
        $this->name = $name;
    }

    public function getValue() {
        return $this->value;
    }

    public function getName() {
        return $this->name;
    }
}
