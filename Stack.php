<?php

class Stack
{
    private $index = 0;

    private $arr = [];

    public function Push($value) {
        $this->arr[$this->index] = $value;
        $this->index++;
    }

    public function Pop() {
        $this->index--;

        if ($this->index == -1) {
            return false;
        }

        return $this->arr[$this->index];
    }
}