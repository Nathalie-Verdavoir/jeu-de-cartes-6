<?php

namespace App\Entity;

class Card
{
    public $color;
    public $value;

    public function __construct($color, Value $value) {
        $this->color = $color;
        $this->value = $value;
    }


    /**
     * Get the value of color
     *
     * @return Color
     */
    public function getColor(): Color
    {
        return $this->color;
    }

    /**
     * Set the value of color
     *
     * @param Color $color
     *
     * @return self
     */
    public function setColor(Color $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get the value of value
     *
     * @return Value
     */
    public function getValue(): Value
    {
        return $this->value;
    }

    /**
     * Set the value of value
     *
     * @param Value $value
     *
     * @return self
     */
    public function setValue(Value $value): self
    {
        $this->value = $value;

        return $this;
    }
}
