<?php

/**
 * Class to manage a scrabble letter
 */
class ScrabbleLetter {
    private $text;
    private $value;
    
    public function __construct($letter, $value) {
        $this->text = $letter;
        $this->value = $value;
    }
    
    public function getText() {
        return $this->text;
    }
    
    public function getValue() {
        return $this->value;
    }
    
    public function getLetterHTML() {
        $tpl = file_get_contents('tpl/letter.tpl.php');
        return strtr($tpl, array(
            '__LETTER__' => $this->getText(),
            '__VALUE__' => $this->getValue()
        ));
    }
}