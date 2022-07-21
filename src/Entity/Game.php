<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Game
{
    /**
     * @Assert\LessThan(53, message ="Vous devez choisir entre 1 et 52 cartes")
     * @Assert\GreaterThan(0, message = "Vous devez choisir entre 1 et 52 cartes")
     */
    private int $num;
    private string $cardsTemplate;
    private Deck $deck;

    public function __construct(int $num = 13, string $cardsTemplate= '') {
        $this->num = $num;
        $this->cardsTemplate = $cardsTemplate;

        /** @var Deck $deck */
        $this->deck = new Deck();
        
    }


    /**
     * Get the value of num
     *
     * @return int
     */
    public function getNum(): int
    {
        return $this->num;
    }

    /**
     * Set the value of num
     *
     * @param int $num
     *
     * @return self
     */
    public function setNum(int $num): self
    {
        $this->num = $num;

        return $this;
    }
    
    /**
     * Get the value of cardsTemplate
     */
    public function getCardsTemplate(): string
    {
            return $this->cardsTemplate;
    }

    /**
     * Set the value of cardsTemplate
     *
     * @param string $cardsTemplate
     *
     * @return self
     */
    public function setCardsTemplate(string $cardsTemplate): self
    {
        $this->cardsTemplate = $cardsTemplate;

        return $this;
    }

    /**
     * Get the value of deck
     *
     * @return Deck
     */
    public function getDeck(): Deck
    {
        return $this->deck;
    }

    /**
     * Set the value of deck
     *
     * @param Deck $deck
     *
     * @return self
     */
    public function setDeck(Deck $deck): self
    {
        $this->deck = $deck;

        return $this;
    }
}
