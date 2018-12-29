<?php

include_once 'class/scrabbletile.class.php';

/**
 * Class to manage a bag containing Scrabble tiles
 */
class ScrabbleBag {
    private $letters; // Array containing letter tiles
    private $lettersNb; // Array containing the number of each letter in the bag
    
    public function __construct($lang='fr') {
        $this->nbTiles = 0;
        
        // Load conf from file containing for each letter the value and number
        $conf = file('conf/'.$lang.'.letters.conf');
        foreach ($conf as $line) {
            $data = explode(',', $line);
            $this->letters[$data[0]] = new ScrabbleTile($data[0], (int)$data[1]);
            $this->lettersNb[$data[0]] = (int)$data[2];
        }
    }
    
    /**
     * Draw a letter from the bag
     * 
     * @param String $letter : Use it to force the letter to draw
     * @return ScrabbleTile : The tile corresponding to the drawn letter
     */
    public function drawLetter($letter='') {
        if(empty($letter)) { // We draw a random tile from the bag
            // Get all remaining letters in the bag
            $pool = $this->getRemainingTiles();
            // Pick a random letter from the bag
            $letter = $pool[rand(0,count($pool)-1)];
        }
        
        $tile = $this->letters[$letter];
        
        // Letter is no longer available in the pool
        $this->lettersNb[$letter]--;

        return $tile;
    }
    
    /**
     * Is the bag empty ?
     * @return boolean
     */
    public function isEmpty() {
        return (array_sum($this->lettersNb) == 0);
    }
    
    /**
     * Generate an array containing all remaining letters from the bag
     * Used to draw a random letter from the remaining ones
     *
     * @return Array : All remaining letters
     */
    private function getRemainingTiles() {
        $remainingLetters = array();
        
        foreach($this->lettersNb as $letter => $nb) {
            for ($i = 0; $i < $nb; $i++) {
                $remainingLetters[] = $letter;
            }
        }
        
        return $remainingLetters;
    }
}