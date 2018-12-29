<?php

/**
 * Class to manage a scrabble tile
 */
class ScrabbleTile {
    private $letter;
    private $value;
    
    public function __construct($letter, $value) {
        $this->letter = $letter;
        $this->value = $value;
    }
    
    public function getTileLetter() {
        return $this->letter;
    }
    
    public function getTileValue() {
        return $this->value;
    }
    
    public function printTile() {
        $tpl = file_get_contents('tpl/letter.tpl.php');
        print strtr($tpl, array(
            '__LETTER__' => $this->getTileLetter(),
            '__VALUE__' => $this->getTileValue()
        ));
    }
}