<?php

include 'class/scrabbleword.class.php';

class ScrabbleBoard
{
    private $boxes;
    private $boardgame;
    
    public function __construct($lang) {
        $this->lineNumber = 15;
        $this->colNumber = 15;
        $this->centralBox = 'H8';
        
        $conf = file('conf/'.$lang.'.board.conf');
        foreach ($conf as $line) {
            $data = explode(',', $line);
            $this->boxes[$data[0]] = trim($data[1]);
        }
        
        $this->boardgame = array();
        for ($l = 0; $l < $this->lineNumber; $l++) {
            $this->boardgame[$l] = array();
            for ($c = 0; $c < $this->colNumber; $c++) {
                $this->boardgame[$l][$c] = '';
            }
        }
    }
    
    public function setWord(&$word) {
        $dir = $this->getDirection($word->getPosition());
        list($x, $y) = $this->idx2i($word->getPosition());
        $score = 0;
        $wd = 0; $wt = 0;
        for ($i = 0; $i < $word->getWordLength(); $i++) {
            $tile = $word->getTile($i);
            $this->setLetter($tile, $this->i2idx($x, $y));
            $lpoints = $tile->getTileValue();
            if($this->boxes[$x][$y] == 'ld') $lpoints*=2;
            if($this->boxes[$x][$y] == 'lt') $lpoints*=3;
            if($this->boxes[$x][$y] == 'wd') $wd++;
            if($this->boxes[$x][$y] == 'wt') $wt++;
            if($dir == 'v') $x++;
            if($dir == 'h') $y++;
            $score+=$lpoints;
        }
        $score*=(2*$wd);
        $score*=(3*$wt);
        $word->score = $score;
    }
    
    /**
     * Sets a letter on the board to the indicated position
     * @param ScrabbleLetter $tile : The tile to set
     * @param String $idx : The position where to set the tile
     */
    public function setLetter($tile, $idx) {
        list($x, $y) = $this->idx2i($idx);
        $this->boardgame[$x][$y] = $tile;
    }
    
    public function unsetLetter($idx) {
        list($x, $y) = $this->idx2i($idx);
        $this->boardgame[$x][$y] = '';
    }
    
    /**
     * Converts an index position into x,y coordinates (e.g. H5 => 7,4)
     * @param String $idx : The alphanum position index
     * @return Array : The x,y coordinates
     */
    private function idx2i($idx) {
        if(!is_numeric(substr($idx,0,1))) {
            $alpha = substr($idx,0,1);
            $numeric = substr($idx,1,2);
        } else {
            $alpha = substr($idx,-1,1);
            $numeric = str_replace($alpha, '', $idx);
        }
        $x = ord($alpha) - 65;
        $y = (int)$numeric - 1;
        return array($x,$y);
    }
    
    /**
     * Converts coordinates into an index position (e.g. 7,4 => H5)
     * @param $x : The x coordinate (line)
     * @param $y : The y coordinate (column)
     * @return String : The alphanum position index
     */
    private function i2idx($i, $j, $dir='h') {
        $x = chr(65 + $i);
        $y = ($j+1);
        return ($dir == 'h') ? ($x.$y) : ($y.$x) ;
    }
    
    public function getBoxType($position) {
        list($x,$y) = $this->idx2i($position);
        return $this->boxes[$x][$y];
    }
    
    public function getBoardHTML() {
        $board = '<table class="board" cellspacing="0">';
        
        // First line with numeric index
        $board.= '<tr class="">';
        $board.= '<td class="idx">&nbsp;</td>';
        for ($i = 0; $i < $this->colNumber; $i++) {
            $y = $i+1;
            $board.= '<td class="idx">';
            $board.= $y;
            $board.= '</td>';
        }
        $board.= '</tr>';
        
        for ($i = 0; $i < $this->lineNumber; $i++) {
            // First column with alphabetic index
            $x = chr(65 + $i);
            $board.= '<tr class="boardline">';
            $board.= '<td class="idx">';
            $board.= $x;
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
     * Search for all position usable on the current board, or regarding
     * 
     * @return Array $boxes : Usable boxes
     */
    public function getBoxesToUse($position='', $direction='') {
       $boxes = array();
       
       // No specific position, we search through the whole board
       if(empty($position)) {
           $emptyBoard = true;
           foreach ($this->boardgame as $i => $line) {
               foreach ($line as $j => $box) {
                   // If the box contains an object (a ScrabbleTile), we search for connected empty boxes
                   if(is_object($box)) {
                       $emptyBoard = false;
                       $boxes = array_merge($boxes, $this->getBoxesAround($i, $j, $direction));
                   }
               }
           }
       } else {
           $emptyBoard = false;
           list($x, $y) = $this->idx2i($position);
           $boxes = $this->getBoxesAround($x, $y, $direction);
       }
       
       // If no box usable, this means the board is empty, so only usable is the central box
       if(empty($boxes) && $emptyBoard) {
           $boxes[] = $this->centralBox;
       }
       
       return array_unique($boxes);
    }
    
    public function getDirection($a, $b='') {
        if(empty($b)) {
            if(is_numeric(substr($a,0,1))) return 'v';
            return 'h';
        } else {
            list($x,$y) = $this->idx2i($a);
            list($i,$j) = $this->idx2i($b);
            if($x == $i) return 'h';
            if($y == $j) return 'v';
        }
    }
    
    /**
     * Search empty boxes around the given position, in the given direction
     * @param int $i : The x coordinate (line)
     * @param int $j : The y coordinate (column)
     * @param string $dir : The direction to search (h or v)
     * @return Array : The list of empty boxes
     */
    private function getBoxesAround($i, $j, $dir='') {
        $boxes = array();
        
        // Search 
        if(empty($dir) || $dir == 'v') {
            // Search up
            $x = $i;
            while(isset($this->boardgame[$x][$j]) && !empty($this->boardgame[$x][$j])) {
                $x--;
            }
            if(isset($this->boardgame[$x][$j])) $boxes[] = $this->i2idx($x, $j);
            
            // Search down
            $x = $i;
            while(isset($this->boardgame[$x][$j]) && !empty($this->boardgame[$x][$j])) {
                $x++;
            }
            if(isset($this->boardgame[$x][$j])) $boxes[] = $this->i2idx($x, $j);
        }
        
        if(empty($dir) || $dir == 'h') {
            // Search left
            $y = $j;
            while(isset($this->boardgame[$i][$y]) && !empty($this->boardgame[$i][$y])) {
                $y--;
            }
            if(isset($this->boardgame[$i][$y])) $boxes[] = $this->i2idx($i, $y);
            
            // Search right
            $y = $j;
            while(isset($this->boardgame[$i][$y]) && !empty($this->boardgame[$i][$y])) {
                $y++;
            }
            if(isset($this->boardgame[$i][$y])) $boxes[] = $this->i2idx($i, $y);
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
        list($i, $j) = $this->idx2i($position);
        // If there is no tile on this position, there is no word to search
        if(empty($this->boardgame[$i][$j])) return array();
        
        $words = array();
        
        // Search for vertical words
        $dir = 'v'; 
        $x = $i;
        $y = $j;
        // We go up the board to find the first letter of the word
        while(isset($this->boardgame[$x][$y]) && is_object($this->boardgame[$x][$y])) {
            $x--;
        }
        $x++;
        $pos = $this->i2idx($x, $y, $dir);
        $sw = new ScrabbleWord($pos);
        while(isset($this->boardgame[$x][$y]) && is_object($this->boardgame[$x][$y])) {
            $sw->addTile($this->boardgame[$x][$y]);
            $x++;
        }
        if($sw->getWordLength() > 1) {
            $sw->setScore($this->calculateScore($sw));
            $words[] = $sw;
        }
        
        // Search for horizontal word
        $x = $i;
        $y = $j;
        $dir = 'h';
        while(isset($this->boardgame[$x][$y]) && is_object($this->boardgame[$x][$y])) {
            $y--;
        }
        $y++;
        $pos = $this->i2idx($x, $y, $dir);
        $sw = new ScrabbleWord($pos);
        while(isset($this->boardgame[$x][$y]) && is_object($this->boardgame[$x][$y])) {
            $sw->addTile($this->boardgame[$x][$y]);
            $y++;
        }
        if($sw->getWordLength() > 1) {
            $sw->setScore($this->calculateScore($sw));
            $words[] = $sw;
        }
        
        return $words;
    }
    
    private function calculateScore($word) {
        
        return 10;
    }
}

