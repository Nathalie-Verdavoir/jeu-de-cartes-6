<?php

namespace App\Tests;

use App\Entity\Color;
use PHPUnit\Framework\TestCase;

class ColorTest extends TestCase
{
    public function provideWholeColors(): array{
        return [
            [Color::C,'Trèfles'], 
            
            [Color::D,'Carreaux'], 
            
            [Color::H,'Coeurs'],
            
            [Color::S,'Piques'],
        ];
    }
    /**
     * @dataProvider provideWholeColors
     */
    public function testColorValueFromName($color,$value): void
    {
        $colorFromValue = Color::From($value);
        $this->assertEquals( $colorFromValue, $color, 'les ' . $value . 'fonctionnent');
        $this->assertTrue(true, 'les ' . $value . 'fonctionnent');
    }
}
