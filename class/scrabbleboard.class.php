<?php

include 'class/scrabbleword.class.php';

class ScrabbleBoard
{
    private $boxes;
    public $boardgame;
    
    public $boardSize;
    
    // Number of lines on the board
    private $lineNumber;
    // Number of columns on the board
    private $colNumber;
    // Central box of the board
    public static $centralBox;
    // Anchors
    public $anchors;
    // Cross-checks
    public $crossCheck;
    
    public function __construct($lang) {
        $this->boardSize = 7;
        $this->lineNumber = $this->boardSize;
        $this->colNumber = $this->boardSize;
        self::$centralBox = (($this->boardSize - 1)/2).','.(($this->boardSize - 1)/2);    
        
        $conf = file('conf/'.$lang.'.board.conf');
        foreach ($conf as $line) {
            $data = explode(',', $line);
            $this->boxes[$data[0]] = trim($data[1]);
        }
        
        $this->boardgame = array();
        for ($l = 0; $l < $this->lineNumber; $l++) {
            $this->boardgame[$l] = array();
            $this->crossCheck[$l] = array();
            for ($c = 0; $c < $this->colNumber; $c++) {
                $this->boardgame[$l][$c] = '';
                $this->crossCheck[$l][$c]['h'] = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
            }
        }
        
        $this->updateAnchors();
    }
    
    /**
     * Maintain the anchors array with position where letters can be set
     */
    public function updateAnchors() {
        $this->anchors = array();
        
        // For each tile on the board, if the tile is empty and next to a ScrabbleLetter, then it's an anchor
        foreach ($this->boardgame as $l => $line) {
            foreach ($line as $c => $box) {
                if(empty($box)) {
                    if(!empty($this->boardgame[$l-1][$c])) $this->anchors[] = "$l,$c"; // Up
                    if(!empty($this->boardgame[$l+1][$c])) $this->anchors[] = "$l,$c"; // Down
                    if(!empty($this->boardgame[$l][$c-1])) $this->anchors[] = "$l,$c"; // Left
                    if(!empty($this->boardgame[$l][$c+1])) $this->anchors[] = "$l,$c"; // Right
                }
            }
        }

        if(empty($this->anchors)) {
            $this->anchors[] = self::$centralBox;
        }
    }
    
    /**
     * Put a word on the board and recalculate anchors
     * 
     * @param ScrabbleWord $sWord Word to put on the board
     */
    public function setWord(ScrabbleWord &$sWord) {
        list($l, $c) = explode(',', $sWord->getIndex());
        for ($i = 0; $i < $sWord->getWordLength(); $i++) {
            $sLet = $sWord->getLetter($i);
            $this->setLetter($sLet, "$l,$c");
            if($sWord->getDirection() == 'v') $l++;
            if($sWord->getDirection() == 'h') $c++;
        }
        
        $this->updateAnchors();
    }
    
    /**
     * Sets a letter on the board to the indicated index
     * 
     * @param ScrabbleLetter $slet The letter to set on the board
     * @param String $idx The index where to set the letter (e.g. 4,6 = line 4, col 6)
     */
    public function setLetter($slet, $idx) {
        list($l, $c) = explode(',', $idx);
        $this->boardgame[$l][$c] = $slet;
    }
    
    /**
     * Remove a letter off the board from the indicated index
     * 
     * @param String $idx The index where to remove the letter (e.g. 4,6 = line 4, col 6)
     * @return ScrabbleLetter The removed letter
     */
    public function unsetLetter($idx) {
        list($l, $c) = explode(',', $idx);
        $slet = $this->boardgame[$l][$c];
        $this->boardgame[$l][$c] = '';
        
        return $slet;
    }
    
    public function getBoxType($idx) {
        list($l, $c) = explode(',', $idx);
        return $this->boxes[$l][$c];
    }
    
    public function getBoardHTML() {
        $board = '<table class="board" cellspacing="0">';
        
        // First line with numeric index
        $board.= '<tr class="">';
        $board.= '<td class="idx">&nbsp;</td>';
        for ($i = 0; $i < $this->colNumber; $i++) {
            $y = $i+1;
            $board.= '<td class="idx">';
            $board.= $i;
            $board.= '</td>';
        }
        $board.= '</tr>';
        
        for ($i = 0; $i < $this->lineNumber; $i++) {
            // First column with alphabetic index
            $x = chr(65 + $i);
            $board.= '<tr class="boardline">';
            $board.= '<td class="idx">';
            $board.= $i;
            $board.= '</td>';
            for ($j = 0; $j < $this->colNumber; $j++) {
                $y = $j+1;
                $class = empty($this->boxes[$x.$y]) ? 'normal' : $this->boxes[$x.$y];
                $letter = empty($this->boardgame[$i][$j]) ? '&nbsp;' : $this->boardgame[$i][$j]->getLetterHTML();
                $board.= '<td class="letterbox '.$class.'" id="'.$x.$y.'">'.$letter.'</td>';
            }
            $board.= '</tr>';
        }
        
        $board.= '</table>';
        
        return $board;
    }
    
    /********************************************************************************
     * FUNCTIONS USED TO SEARCH POSSIBLE SOLUTIONS ON THE BOARD
     ********************************************************************************/
    /**
     * Search for all positions usable on the current board, or regarding an index
     * 
     * @param $idx String The index to search from
     * @param $dir String The direction to search to
     * 
     * @return Array $boxes Array containing index of usable boxes
     */
    public function getBoxesToUse($idx='', $dir='') {
       $boxes = array();
       
       // No specific position, we search through the whole board
       if(empty($idx)) {
           $emptyBoard = true;
           foreach ($this->boardgame as $l => $line) {
               foreach ($line as $c => $box) {
                   // If the box contains an object (a ScrabbleLetter), we search for connected empty boxes
                   if(is_object($box)) {
                       $emptyBoard = false;
                       $boxes = array_merge($boxes, $this->getBoxesAround("$l,$c", $dir));
                   }
               }
           }
       } else {
           $emptyBoard = false;
           $boxes = $this->getBoxesAround($idx, $dir);
       }
       
       // If no box usable, this means the board is empty, so only usable is the central box
       if(empty($boxes) && $emptyBoard) {
           $boxes[] = $this->centralBox;
       }
       
       return array_unique($boxes);
    }
    
    /**
     * Get the direction (h or v) regarding 2 indexes
     * 
     * @param String $idx1 First index
     * @param String $idx2 Second index
     * @return String h or v, false if the 2 indexes are not on the same line or column
     */
    public function getDirection($idx1, $idx2) {
        list($l1, $c1) = explode(',', $idx1);
        list($l2, $c2) = explode(',', $idx2);
        if($l1 == $l2) return 'h'; // Indexes are on the same line, direction is horizontal
        if($c1 == $c2) return 'v'; // Indexes are on the same column, direction is vertical
        
        return false;
    }
    
    /**
     * Search empty boxes around the given position, in the given direction
     * 
     * @param String $idx An index value (e.g. 4,6 = line 4, col 6)
     * @param String $dir The direction to search (h or v)
     * @return Array : The list of empty boxes
     */
    private function getBoxesAround($idx, $dir='') {
        list($l, $c) = explode(',', $idx);
        $boxes = array();
        
        // Search 
        if(empty($dir) || $dir == 'v') {
            // Search up
            $x = $l;
            while(isset($this->boardgame[$x][$c]) && !empty($this->boardgame[$x][$c])) {
                $x--;
            }
            if(isset($this->boardgame[$x][$c])) $boxes[] = "$x,$c";
            
            // Search down
            $x = $l;
            while(isset($this->boardgame[$x][$c]) && !empty($this->boardgame[$x][$c])) {
                $x++;
            }
            if(isset($this->boardgame[$x][$c])) $boxes[] = "$x,$c";
        }
        
        if(empty($dir) || $dir == 'h') {
            // Search left
            $y = $c;
            while(isset($this->boardgame[$l][$y]) && !empty($this->boardgame[$l][$y])) {
                $y--;
            }
            if(isset($this->boardgame[$l][$y])) $boxes[] = "$l,$y";
            
            // Search right
            $y = $c;
            while(isset($this->boardgame[$l][$y]) && !empty($this->boardgame[$l][$y])) {
                $y++;
            }
            if(isset($this->boardgame[$l][$y])) $boxes[] = "$l,$y";
        }
        
        return $boxes;
    }
    
    /**
     * Search words from a position
     * 
     * @param String $position : Alphanumeric position to search from
     * @return Array : Words found
     */
    public function searchWords($position) {
        list($l, $c) = explode(',', $position);
        // If there is no tile on this position, there is no word to search
        if(empty($this->boardgame[$l][$c])) return array();
        
        $words = array();
        
        // Search for vertical words
        $dir = 'v'; 
        $x = $l;
        $y = $c;
        // We go up the board to find the first letter of the word
        while(isset($this->boardgame[$x][$y]) && is_object($this->boardgame[$x][$y])) {
            $x--;
        }
        $x++;
        $idx = "$x,$y";
        $sw = new ScrabbleWord($idx, $dir, $this->idx2an($idx, $dir));
        while(isset($this->boardgame[$x][$y]) && is_object($this->boardgame[$x][$y])) {
            $sw->addLetter($this->boardgame[$x][$y]);
            $x++;
        }
        if($sw->getWordLength() > 1) {
            $this->calculateScore($sw);
            $words[] = $sw;
        }
        
        // Search for horizontal word
        $x = $l;
        $y = $c;
        $dir = 'h';
        while(isset($this->boardgame[$x][$y]) && is_object($this->boardgame[$x][$y])) {
            $y--;
        }
        $y++;
        $idx = "$x,$y";
        $sw = new ScrabbleWord($idx, $dir, $this->idx2an($idx, $dir));
        while(isset($this->boardgame[$x][$y]) && is_object($this->boardgame[$x][$y])) {
            $sw->addLetter($this->boardgame[$x][$y]);
            $y++;
        }
        if($sw->getWordLength() > 1) {
            $this->calculateScore($sw);
            $words[] = $sw;
        }
        
        return $words;
    }
    
    private function calculateScore($word) {
        list($l, $c) = explode(',', $word->getIndex());
        
        $points = 0;
        $wd = 0; $wt = 0;
        for ($i = 0; $i < $word->getWordLength(); $i++) {
            $lVal = $word->getLetter($i)->getValue();
            $pos = $this->idx2an("$l,$c");
            if(isset($this->boxes[$pos])) {
                if($this->boxes[$pos] == 'ld') $lVal*=2;
                if($this->boxes[$pos] == 'lt') $lVal*=3;
                if($this->boxes[$pos] == 'wd') $wd++;
                if($this->boxes[$pos] == 'wt') $wt++;
            }
            if($word->getDirection() == 'v') $l++;
            if($word->getDirection() == 'h') $c++;
            $points+=$lVal;
        }
        $points*= pow(2, $wd);
        $points*= pow(3, $wt);
        
        $word->setPoints($points);
        
        //return 10;
    }
    
    /**
     * Converts a box index into an alphanumeric position (e.g. 7,4 h => H5)
     *
     * @param String $idx The index value (e.g. 4,6 = line 4, col 6)
     * @param String $dir The direction wanted (h or v)
     * @return String : The alphanum position index
     */
    private function idx2an($idx, $dir='h') {
        list($l, $c) = explode(',', $idx);
        $a = chr(65 + (int)$l);
        $n = ((int)$c+1);
        return ($dir == 'h') ? ($a.$n) : ($n.$a) ;
    }
    
    private function an2idx($an, $dir='h') {
        if(!is_numeric(substr($an,0,1))) {
            $alpha = substr($an,0,1);
        } else {
            $alpha = substr($an,-1);
        }
        $num = (int)str_replace($alpha, '', $an);
        $l = (int)(ord($alpha) - 65);
        $c = $num - 1;
        return "$l,$c";
    }
}

