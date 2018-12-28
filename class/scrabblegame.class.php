<?php

class ScrabbleGame
{
    // Game lang
    public $lang;
    
    // Game board
    public $board;
    
    // Game dictionnary
    private $dict;
    
    // Letter values
    private $LetterValues;
    
    // Letter number
    private $LetterPool;
    
    // Number of letters for a draw
    private $NumberLetterDraw;
    
    // Current draw
    public $currentDraw;
    
    /**
     * Load the game setup according to the lang, initialize the dictionnary and the board
     * 
     * @param String $lang : Lang in which the game is played
     */
    public function __construct($lang) {
        $this->lang = $lang;
        $this->NumberLetterDraw = 7;
        
        // Load dictionnary
        $this->dict = pspell_new($lang);
        
        // Load conf from file containing for each letter the value and number
        $conf = file('conf/'.$lang.'.conf');
        foreach ($conf as $line) {
            $data = explode(',', $line);
            $this->LetterValues[$data[0]] = (int)$data[1];
            $this->LetterPool[$data[0]] = (int)$data[2];
        }
        
        // Load board
        // @TODO dev
    }
    
    /**
     * Launch a new turn to the game
     * 
     * @param String $rest : Remaining letters from previous turn
     */
    public function newTurn($rest='') {
        // Draw new letters
        $this->draw($rest);
        echo '<hr>DRAW : '.$this->currentDraw;
        
        $words = $this->getAllCorrectWords();
        echo ' => POSSIBLE WORDS : '.count($words);
        echo '<hr>';
        
        foreach ($words as $word) {
            echo '<br> - '.$word.' : '.$this->getWordValue($word).' points';
            if(strlen($word) == 7) echo ' => SCRABBLE !!';
        }
    }
    
    /**
     * Draw letters from the remaining pool
     * 
     * @param string $rest : Remaining letters from previous turn
     */
    public function draw($rest='') {
        $this->currentDraw = $rest;
        
        // Get all remaining letters
        $pool = $this->getLetterPool();
        
        while(strlen($this->currentDraw) < $this->NumberLetterDraw && !empty($pool)) {
            // Pick a random letter from the pool
            $letter = $pool[rand(0,count($pool)-1)];
            // Letter is no longer available in the pool
            $this->LetterPool[$letter]--;
            // Add letter to the current draw
            $this->currentDraw.= $letter;
            // Get all remaining letters again
            $pool = $this->getLetterPool();
        }
    }
    
    /**
     * Generate an array containing all remaining letters from the pool
     * 
     * @return Array : All remaining letters
     */
    private function getLetterPool() {
        $remainingLetters = array();
        
        foreach($this->LetterPool as $letter => $nb) {
            for ($i = 0; $i < $nb; $i++) {
                $remainingLetters[] = $letter;
            }
        }
        
        return $remainingLetters;
    }
    
    /**
     * Get all correct words from the current letter draw
     * 
     * @return Array : Words
     */
    public function getAllCorrectWords() {
        $letterCombinations = array();
        $letters = str_split($this->currentDraw);
        $this->getAllLetterCombinations($letters, $letterCombinations);
        $letterCombinations = array_unique($letterCombinations);
        
        $words = array();
        
        foreach ($letterCombinations as $word) {
            if($this->isWordValid($word)) $words[] = $word;
        }
        
        return $words;
    }
    
    /**
     * Generate an array containing all possible combination from an array of letters
     * 
     * @param Array $letters : Letters to combine
     * @param Array $results : All combinations possible
     */
    private function getAllLetterCombinations(&$letters, &$results) {
        for ($i = 0; $i < count($letters); $i++)
        {
            $results[] = $letters[$i];
            $tempset = $letters;
            array_splice($tempset, $i, 1);
            $tempresults = array();
            $this->getAllLetterCombinations($tempset, $tempresults);
            foreach ($tempresults as $res)
            {
                $results[] = $letters[$i] . $res;
            }
        }
    }
    
    /**
     * Check if the word is valid in the dictionnary
     * 
     * @param String $word : The word to check
     * @return boolean : true if the word is correct, false otherwise 
     */
    public function isWordValid($word) {
        // Check the word in the dictionnary
        if(!pspell_check($this->dict, $word)) return false;
        
        return true;
    }
    
    /**
     * Get the value of a word, sum of its letter values
     * 
     * @param String $word : The word we want the value of
     * @return int : Value of the word
     */
    public function getWordValue($word) {
        $points = 0;
        $word = str_split($word);
        foreach ($word as $l) {
            $points+= $this->getLetterValue($l);
        }
        
        return $points;
    }
    
    /**
     * Get the value of a letter
     *
     * @param String $letter : The letter for which we want the value
     * @return int : Value of the letter, 0 if not found
     */
    public function getLetterValue($letter) {
        $letter = strtoupper($letter);
        return empty($this->LetterValues[$letter]) ? 0 : $this->LetterValues[$letter];
    }
}

