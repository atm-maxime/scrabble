<?php

/**
 * Class to manage a scrabble word
 */
class ScrabbleWord {
    private $letters;
    private $position;
    private $points;
    
    public function __construct($position) {
        $this->position = $position;
        $this->letters = array();
    }
    
    public function getPosition() {
        return $this->position;
    }
    
    public function getPoints() {
        return $this->points;
    }
    
    public function setPoints($points) {
        $this->points = $points;
    }
    
    public function addLetter($sletter) {
        $this->letters[] = $sletter;
    }
    
    public function getLetters() {
        return $this->tiles;
    }
    
    public function getLetter($iSlet) {
        return $this->letters[$iSlet];
    }
    
    public function getWordLength() {
        return count($this->letters);
    }
    
    public function getWordAsText() {
        $word = '';
        
        foreach ($this->letters as $slet) {
            $word.= $slet->getText();
        }
        
        return $word;
    }
}