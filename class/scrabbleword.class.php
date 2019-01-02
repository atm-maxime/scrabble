<?php

/**
 * Class to manage a scrabble word
 */
class ScrabbleWord {
    private $letters;
    private $index;
    private $direction;
    private $position;
    private $points;
    
    public function __construct($idx, $dir, $pos) {
        $this->index = $idx;
        $this->direction = $dir;
        $this->position = $pos;
        $this->letters = array();
    }
    
    public function getIndex() {
        return $this->index;
    }
    
    public function getDirection() {
        return $this->direction;
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