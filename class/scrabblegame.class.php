<?php

ini_set('memory_limit', '500M');
include_once 'class/scrabblebag.class.php';
include_once 'class/scrabbleboard.class.php';
include_once 'class/scrabbledict.class.php';
include_once 'class/scrabblesolver.class.php';

class ScrabbleGame
{
    // Game lang, used to load setup and dictionnary
    private $lang;
    // Game dictionnary, using PHP pspell library
    public $dict;
    
    // Letters bag containing ScrabbleLetters
    private $bag;
    // Board game
    public $board;
    // Game turns
    private $gameTurns;
    // Game score
    private $score;
    
    // Number of letters for a draw, usually 7
    private $numberLetterDraw;
    
    // Current draw
    public $currentDraw;
    // Current solutions possible with current draw
    public $currentSolutions;
    // Current words possible with current draw (= only existing words from current solutions + scores) 
    public $currentWords;
    
    /**
     * Load the game setup according to the lang, initialize the dictionnary and the board
     * 
     * @param String $lang : Lang in which the game is played
     */
    public function __construct($lang='fr') {
        $this->lang = $lang;
        
        // Load dictionnary
        $this->dict = new ScrabbleDict($lang);
        // Load scrabble bag
        $this->bag = new ScrabbleBag($lang);
        // Load board
        $this->board = new ScrabbleBoard($lang);
        $this->numberLetterDraw = ($this->board->boardSize - 1) / 2;
        
        // Game current turn
        $this->currentDraw = array();
        $this->currentWords = array();
        $this->currentSolutions = array();
        
        // Game results
        $this->gameTurns = array();
        $this->score = 0;
    }
    
    /**
     * Launch a new turn to the game
     * 
     * @param String $emptyDraw Do we need to empty the deck of letters ?
     */
    public function newTurn($emptyDraw=false) {
        // Turn initialization
        $this->currentWords = array();
        $this->currentSolutions = array();
        if($emptyDraw) {
            $this->bag->putNLetters($this->currentDraw);
            $this->currentDraw = array();
        }
        
        // How many letters we need to draw ?
        $nbToDraw = $this->numberLetterDraw - count($this->currentDraw);
        // We draw them
        $sletters = $this->bag->drawNLetters($nbToDraw);
        // We add them to the current draw
        $this->currentDraw = array_merge($this->currentDraw, $sletters);
    }
    
    /**
     * Search for possible words to place on the board
     */
    public function getPossibleWords() {
        $solver = new ScrabbleSolver();
        $solver->searchWords($this->dict, $this->board, $this->currentDraw);
        
        $this->currentWords = array();
        //$this->getSolutions2($this->board, $this->currentDraw);
        //$this->getAllCorrectWords();
        //print_r($TTest);
//         $total = 0;
//         foreach ($TTest as $line => $data) {
//             foreach ($data as $col => $words) {
//                 echo '<br>'.$line.' : '.$col.' => '.count($words);
//                 $total+=count($words);
//             }
//         }
//         echo '<br>TOT : '.$total;
//         print_r($TTest);
    }
    
    
    /**
     * Search for every word combination possible with the current draw and the current board
     * - 1 : Search on the board the list of boxes next to already played letters
     * - 2 : For each boxes, set a letter on it
     * - 3 : Search what words are created with this new letter and save it if all words are correct
     * - 4 : Search the next boxes that can be used
     * - 5 : For each box, set the next letter on it
     * ... and so on recursively
     * 
     * Finally calculate the score of each word that has been saved and sort it
     * Priority to horizontal words and the more on the top left possible 
     */
    public function getSolutions2(&$board, &$letters, $tested=array(), $position='', $direction='h') {
        global $test, $TTest;
        $boxes = $board->getBoxesToUse($position, $direction);
        
        foreach ($boxes as $pos) { // for each possible box on the board
            foreach ($letters as $i => $slet) { // for each letter on the current draw
                unset($letters[$i]); // Remove letter from the current draw
                //echo '<br>'.$l.','.$c.' - ';
                //print_r($tested);
                // First letter put on the board doesn't give direction, 2nd is
                $d = $this->numberLetterDraw - count($letters);
                if($d > 1) $direction = $board->getDirection($position, $pos);
                
                $board->setLetter($slet, $pos); // Puts letter on the board
                //print $this->getBoardHTML();
                $words = $this->board->searchWords($pos); // Search for words made with this new letter
                
                $this->currentWords = array_merge($this->currentWords, $words);
                
                if(!empty($letters)) {
                    $this->getSolutions2($board, $letters, $tested, $pos, $direction); // Do it again, recursively
                }
                
                $board->unsetLetter($pos); // Remove letter from the board
                $letters[$i] = $slet;  // Letter goes back in the current draw
            }
        }
    }
    
    public function getSolutions($position='', $direction='') {
        $boxes = $this->board->getBoxesToUse($position, $direction);
        
        //         print '<hr>GET SOLUTIONS FOR LETTERS : '.$this->tmpGetCurrentDrawText().'<hr>';
        //         print 'Boxes found : '.implode(' - ', $boxes);
        //         print '<br>---------------';
        
        foreach ($boxes as $pos) { // for each possible box on the board
            foreach ($this->currentDraw as $i => $slet) { // for each letter on the current draw
                unset($this->currentDraw[$i]); // Remove letter from the current draw
                
                // First letter put on the board doesn't give direction, 2nd is
                $l = $this->numberLetterDraw - count($this->currentDraw);
                if($l > 1) $direction = $this->board->getDirection($position, $pos);
                
                $this->board->setLetter($slet, $pos); // Puts letter on the board
                $words = $this->board->searchWords($pos); // Search for words made with this new letter
                // Marche bien mais ne prend que les mots formé par la lettre $i à la position $pos,
                // ne prend pas en compte les mots formé par la lettre précédente par exemple...
                //                 print '<br>PUT '.$slet->getText().' IN '.$pos;
                
                $this->currentWords = array_merge($this->currentWords, $words);
                
                if(!empty($this->currentDraw)) {
                    $this->getSolutions($pos, $direction); // Do it again, recursively
                }
                
                //                 print '<br>Solutions => ';
                //                 foreach ($words as $w) {
                //                     print $w->getWordAsText().', ';
                //                 }
                    $this->board->unsetLetter($pos); // Remove letter from the board
                    $this->currentDraw[$i] = $slet;  // Letter goes back in the current draw
            }
            //flush();
            //             print '<br>Next box...';
        }
    }
    
    /**
     * Get all correct words from the current letter draw
     * 
     * @return Array : Words
     */
    public function getAllCorrectWords() {
        /*
        $letterCombinations = array();
        $letters = array_keys($this->currentDraw);
        $this->getAllLetterCombinations($letters, $letterCombinations);
        $letterCombinations = array_unique($letterCombinations);
        */
        
        $this->dict = pspell_new($this->lang);
        //$this->currentWords = array();
        
        foreach ($this->currentWords as $i => $w) {
            if(!$this->isWordValid($w->getWordAsText())) {
                unset($this->currentWords[$i]);
            }
        }
        
        usort($this->currentWords, 'sort_soutions_by_points');
//         usort($this->currentWords, 'sort_soutions_by_position');
    }
    
    /**
     * Generate an array containing all possible combination from an array of letters
     * 
     * @param Array $letters : Letters to combine
     * @param Array $results : All combinations possible
     */
    /*private function getAllLetterCombinations(&$letters, &$results) {
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
    }*/
    
    
    /**
     * Get the value of a word, sum of its letter values
     *
     * @param String $word : The word we want the value of
     * @return int : Value of the word
     */
    /*public function getWordValue($word, $pos) {
        $points = 0;
        $word = str_split($word);
        foreach ($word as $l) {
            $lpoints = $this->bag->getLetterValue($l);
            $points += $lpoints;
        }
        
        return $points;
    }*/
    
    /**
     * Check if the word is valid in the dictionnary
     * 
     * @param String $word : The word to check
     * @return boolean : true if the word is correct, false otherwise 
     */
    public function isWordValid($word) {
        $this->dict = pspell_new($this->lang);
        
        // Check the word in the dictionnary
        if(!pspell_check($this->dict, $word)) return false;
        
        return true;
    }
    
    /**
     * Selection of a word amongst possible words to play the turn
     * 
     * @param int $iWord : Word identifier in currentWords array
     */
    public function selectWord($iWord) {
        $this->board->setWord($this->currentWords[$iWord]);
        // @TODO : remove placed letters from currentdraw
        $this->gameTurns[] = $this->currentWords[$iWord];
        $this->score+= $this->currentWords[$iWord]->getPoints();
    }
    
    /**
     * Print the board of the game
     */
    public function getBoardHTML() {
        return $this->board->getBoardHTML();
    }
    
    public function tmpGetCurrentDrawText() {
        $w = '';
        foreach ($this->currentDraw as $slet) {
            $w.= $slet->getText();
        };
        return $w;
    }
    
    /**
     * Print the draw of the current turn
     */
    public function getCurrentDrawHTML() {
        $draw = array();
        $i = 0;
        $tpl = file_get_contents('tpl/draw.tpl.php');
        foreach ($this->currentDraw as $slet) {
            $draw['__'.$i.'__'] = $slet->getLetterHTML();
            $i++;
        }
        for ($i; $i < $this->numberLetterDraw - count($this->currentDraw); $i++) {
            $slet = new ScrabbleLetter('', '');
            $draw['__'.$i.'__'] = $slet->getLetterHTML();
        }
        
        return strtr($tpl, $draw);
    }
    
    /**
     * Print the possible words of the current turn
     */
    public function getWordsHTML() {
        $words = '<table width="100%">';
        foreach ($this->currentWords as $i => $word) {
            $words.= '<tr>
                    <td>'.$word->getWordAsText().'</td>
                    <td>'.$word->getPosition().'</td>
                    <td>'.$word->getPoints().'</td>
                    <td><i class="fas fa-check-circle word" iword="'.$i.'"></i></td>
                    </tr>';
        }
        $words.= '</table>';
        
        return $words;
    }
    
    /**
     * Get the history of words played in the game
     */
    public function getGameTurnsHTML() {
        $turns = '<table width="100%">';
        foreach ($this->gameTurns as $i => $word) {
            $turns.= '<tr>
                    <td>'.($i+1).'</td>
                    <td>'.$word->word.'</td>
                    <td>'.$word->position.'</td>
                    <td>'.$word->score.'</td>
                    </tr>';
        }
        $turns.= '</table>';
        
        return $turns;
    }
    
    public function getAnchors() {
        return implode('<br>', $this->board->anchors);
    }
}
