<?php

/**
 * Class to manage a scrabble word
 */
class ScrabbleWord {
    // Array containing the ScrabbleLetters forming the word
    private $letters;
    // Index where the word begins
    private $index;
    // Direction of the word (h or v)
    private $direction;
    // Alphanumeric position of the word (i.e. H3)
    private $position;
    // Score of the word in the game
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