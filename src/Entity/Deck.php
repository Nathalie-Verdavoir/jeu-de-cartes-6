<?php

namespace App\Entity;


class Deck
{
    private array $goodOrderColors;
    private array $goodOrderValues;
    private $mixedColors;
    private $mixedValues;
    private $mixedDeck;
    private $fullDeck;

    public function __construct() {
        $this->mixedColors = $this->goodOrderColors = Color::cases();
        
        $this->mixedColors = $this->random($this->mixedColors);
       
        $this->mixedValues = $this->goodOrderValues = Value::cases();
        $this->mixedValues = $this->random($this->mixedValues);
        
        $this->mixedCards();
        $this->fullSetOfCards();
    }


    public function random($suit): array
    {
        $newArray = [];
        $keys = array_keys($suit);
        shuffle($keys);
        foreach($keys as $key) {
           
            $newArray[$key] =  $suit[$key];
        } 
        return $newArray;
    }

    public function fullSetOfCards()
    {
        foreach ($this->goodOrderColors AS $color) {
            foreach ($this->goodOrderValues AS $value) {
                $fullDeck[] = new Card($color,$value);
                $this->fullDeck = $fullDeck;
            }
        }
    }

    public function mixedCards()
    {
        foreach ($this->mixedColors AS $color) {
            foreach ($this->mixedValues AS $value) {
                /** @var  Value $value */
                $mixedDeck[] = new Card($color,$value);
                $this->mixedDeck = $mixedDeck;
            }
        }
    }

    /**
     * Get the value of mixedDeck
     *
     * @return array
     */
    public function getMixedDeck(): array
    {
        return $this->mixedDeck;
    }
    
    /**
     * Get the value of goodOrderColors
     *
     * @return array
     */
    public function getGoodOrderColors(): array
    {
        return $this->goodOrderColors;
    }

    /**
     * Set the value of goodOrderColors
     *
     * @param array $goodOrderColors
     *
     * @return self
     */
    public function setGoodOrderColors(array $goodOrderColors): self
    {
        $this->goodOrderColors = $goodOrderColors;

        return $this;
    }

    /**
     * Get the value of goodOrderValues
     *
     * @return array
     */
    public function getGoodOrderValues(): array
    {
        return $this->goodOrderValues;
    }

    /**
     * Set the value of goodOrderValues
     *
     * @param array $goodOrderValues
     *
     * @return self
     */
    public function setGoodOrderValues(array $goodOrderValues): self
    {
        $this->goodOrderValues = $goodOrderValues;

        return $this;
    }

   

    /**
     * Get the value of mixedValues
     *
     * 
     */
    public function getMixedColors()
    {
        return $this->mixedColors;
    }

    /**
     * Set the value of mixedValues
     *
     * 
     *
     * @return self
     */
    public function setMixedColors($mixedColors): self
    {
        $this->mixedColors = $mixedColors;

        return $this;
    }
    

    /**
     * Get the value of mixedValues
     *
     * 
     */
    public function getMixedValues()
    {
        return $this->mixedValues;
    }

    /**
     * Set the value of mixedValues
     *
     * 
     *
     * @return self
     */
    public function setMixedValues(array $mixedValues): self
    {
        $this->mixedValues = $mixedValues;

        return $this;
    }

    /**
     * Get the value of fullDeck
     *
     * @return array
     */
    public function getFullDeck(): array
    {
        return $this->fullDeck;
    }

    /**
     * Set the value of fullDeck
     *
     * @param array $fullDeck
     *
     * @return self
     */
    public function setFullDeck(array $fullDeck): self
    {
        $this->fullDeck = $fullDeck;

        return $this;
    }
}
