<?php

include_once 'class/scrabbleletter.class.php';

/**
 * Class to manage a bag containing Scrabble letters
 */
class ScrabbleBag {
    private $letters; // Array containing Scrabble letter
    
    public function __construct($lang='fr') {
        $this->letters = array();
        
        // Load conf from file containing for each letter the value and number
        $conf = file('conf/'.$lang.'.letters.conf');
        foreach ($conf as $line) {
            $data = explode(',', $line);
            for ($i = 0; $i < (int)$data[2]; $i++) {
                $this->letters[] = new ScrabbleLetter($data[0], (int)$data[1]);
            }
        }
    }
    
    /**
     * Is the bag empty ?
     * @return boolean
     */
    public function isEmpty() {
        return (count($this->letters) == 0);
    }
    
    /**
     * Give the number of letters in the bag
     * 
     * @return int Number of Scrabble letters remaining in the bag
     */
    public function getNbLetters() {
        return count($this->letters);
    }
    
    /**
     * Give the number of letters in the bag by letter (e.g. A => 3, B => 1, ...)
     * 
     * @return Array containing the number of letters remaining in the bag by letter
     */
    public function getNbByLetter() {
        $res = array();
        foreach ($this->letters as $slet) {
            $l = $slet->getText();
            if(empty($res[$l])) $res[$l] = 0;
            $res[$l]++;
        }
        return $res;
    }
    
    /**
     * Draw a letter from the bag
     * 
     * @param String $letter Use it to force the letter to draw
     * @return ScrabbleLetter The Scrabbleletter corresponding to the drawn letter
     */
    public function draw1Letter($letter='') {
        if($this->isEmpty()) return false;
        
        $iLet = false;
        if(empty($letter)) { // We draw a random letter from the bag
            $iLet = array_rand($this->letters);
        } else { // We search for a letter corresponding to $letter
            foreach ($this->letters as $i => $slet) {
                if($slet->getText() == $letter) {
                    $iLet = $i;
                    break;
                }
            }
        }
        
        if($iLet !== false) {
            $slet = $this->letters[$iLet];
            unset($this->letters[$iLet]);
        }

        return $slet;
    }
    
    /**
     * Draw several letters from the bag
     *
     * @param int $number The number of letters to draw
     * @return Array Array containing the drawn Scrabbleletters
     */
    public function drawNLetters($number=1) {
        $sletters = array();
        for ($i = 0; $i < $number; $i++) {
            if($slet = $this->draw1Letter()) $sletters[] = $slet;
            else break;
        }
        
        return $sletters;
    }
}