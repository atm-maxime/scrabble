<?php

include_once 'class/scrabblebag.class.php';
include_once 'class/scrabbleboard.class.php';

class ScrabbleGame
{
    // Game lang, used to load setup and dictionnary
    private $lang;
    // Game dictionnary, using PHP pspell library
    private $dict;
    
    // Letters distribution, array containing the number of occurrence of each letter
    private $bag;
    // Board game
    private $board;
    // Game turns
    private $gameTurns;
    
    // Number of letters for a draw, usually 7
    private $numberLetterDraw;
    
    // Current draw
    public $currentDraw;
    // Current words possible with current draw
    public $currentWords;
    
    /**
     * Load the game setup according to the lang, initialize the dictionnary and the board
     * 
     * @param String $lang : Lang in which the game is played
     */
    public function __construct($lang='fr') {
        $this->lang = $lang;
        $this->numberLetterDraw = 7;
        $this->currentDraw = array();
        $this->currentWords = array();
        
        // Load dictionnary
        $this->dict = pspell_new($lang);
        
        // Load scrabble bag
        $this->bag = new ScrabbleBag($lang);
        
        // Load board
        $this->board = new ScrabbleBoard($lang);
        
        // Game turns
        $this->gameTurns = array();
    }
    
    /**
     * Launch a new turn to the game
     * 
     * @param String $rest : Remaining letters from previous turn
     */
    public function newTurn($rest='') {
        // Draw new letters
        $this->draw($rest);
        
        // Get all words possible
        $this->getAllCorrectWords();
        
        $this->board->setLetter(array_rand($this->currentDraw), 'B2');
        
        // @TODO continue...
    }
    
    /**
     * Draw letters from the remaining pool
     * 
     * @param Array $rest : Remaining letters from previous turn
     */
    public function draw($rest=array()) {
        $this->currentDraw = $rest;
        
        while(count($this->currentDraw) < $this->numberLetterDraw && !$this->bag->isEmpty()) {
            // Pick a random letter from the pool
            $tile = $this->bag->drawLetter();
            // Add letter to the current draw
            $this->currentDraw[$tile->getTileLetter()] = $tile;
        }
    }
    
    /**
     * Get all correct words from the current letter draw
     * 
     * @return Array : Words
     */
    public function getAllCorrectWords() {
        $letterCombinations = array();
        $letters = array_keys($this->currentDraw);
        $this->getAllLetterCombinations($letters, $letterCombinations);
        $letterCombinations = array_unique($letterCombinations);
        
        $this->dict = pspell_new($this->lang);
        $this->currentWords = array();
        
        foreach ($letterCombinations as $word) {
            if($this->isWordValid($word)) {
                $w = new stdClass();
                $w->word = $word;
                $w->score = $this->getWordValue($word);
                $w->position = 'H5';
                $this->currentWords[] = $w;
            }
        }
        
        $this->currentWords;
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
    
    public function selectWord($iWord) {
        $this->gameTurns[] = $this->currentWords[$iWord];
        $this->score+= $this->currentWords[$iWord]->score;
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
            $points+= $this->bag->getLetterValue($l);
        }
        
        return $points;
    }
    
    /**
     * Print the board of the game
     */
    public function printBoard() {
        $this->board->printBoard();
    }
    
    /**
     * Print the draw of the current turn
     */
    public function printDraw() {
        $draw = array();
        $i = 0;
        $tpl = file_get_contents('tpl/draw.tpl.php');
        foreach ($this->currentDraw as $letter) {
            $draw['__'.$i.'__'] = $letter->getTileHTML();
            $i++;
        }
        for ($i; $i < $this->numberLetterDraw - count($this->currentDraw); $i++) {
            $tile = new ScrabbleTile('', '');
            $draw['__'.$i.'__'] = $tile->getTileHTML();
        }
        
        print strtr($tpl, $draw);
    }
    
    /**
     * Print the possible words of the current turn
     */
    public function printWords() {
        print '<table width="100%">';
        foreach ($this->currentWords as $word) {
            print '<tr>
                    <td>'.$word->word.'</td>
                    <td>'.$word->position.'</td>
                    <td>'.$word->score.'</td>
                    </tr>';
        }
        print '</table>';
    }
    
    /**
     * Print the history of words played
     */
    public function printGameTurns() {
        print '<table width="100%">';
        foreach ($this->gameTurns as $word) {
            print '<tr>
                    <td>'.$word->word.'</td>
                    <td>'.$word->position.'</td>
                    <td>'.$word->score.'</td>
                    </tr>';
        }
        print '</table>';
    }
}