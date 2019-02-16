<?php
/*
 * Copyright (C) 2019       Maxime Kohlhaas     <maximekohlhaas@gmail.com>
 */

/**
 * Class to find all possible scrabble moves given
 *  - A boardgame
 *  - A dictionary
 *  - A letter rack
 * Uses "The World's Fastest Scrabble Program" principles
 */
class ScrabbleSolver {
    
    /**
     * Search for possible words based on the board and letters of the turn
     * 
     * @param ScrabbleDict $dict The dictionary that validates words
     * @param ScrabbleBoard $board The board game
     * @param array $letters The rack of letters
     */
    function searchWords(ScrabbleDict $sDict, ScrabbleBoard &$sBoard, Array $letters) {
        // To search words, we need anchors and crosschecks
        $board = $sBoard->boardgame;
        $anchors = $this->getAnchors($board);
        print_r($anchors);
        $crosscheck = $this->getCrossCheck($board, $sDict, $anchors);
        
        foreach($anchors as $idx) {
            $this->leftPart('', $sDict->words, $board, $letters, $idx, $crosscheck);
        }
        
        exit;
        
        // Now the same transposed
        $board = $this->transposeBoard($sBoard->boardgame);
        $anchors = $this->getAnchors($board->boardgame);
        $crosscheck = $this->crossCheckWords($board, $sDict, $anchors);
        
        foreach($anchors as $idx) {
            $this->leftPart('', $sDict->words, $board, $letters, $idx, $crosscheck);
        }
    }
    
    private function leftPart(String $partialWord, Array $dict, Array $board, Array $letters, String $idx, Array $crosscheck) {
        
    }
    
    private function extendRight() {
        
    }
    
    /**
     * Search for each anchor which letters are possible regarding cross-check words
     * @param ScrabbleDict $dict The game dictionnary
     * @param String $dir The direction to test (h or v)
     */
    public function getCrossCheck(Array $board, ScrabbleDict $sDict, Array $anchors) {
        $crosscheck = array();
        
        foreach($anchors as $idx) {
            echo '<hr>'.$idx.'<hr>';
            list($l,$c) = explode(',', $idx);
            $crosscheck[$l][$c] = array(); // Empty possible letters for the anchor
            $curDict = $sDict->words;
            
            $i = $c-1;
            while($i >= 0 && !empty($board[$l][$i])) {
                $i--;
            }
            $i++;
            echo $i;
            while ($i < $c) {
                /*$curDict = $curDict[$board[$l][$i]->getText()];
                
                foreach($curDict as $letter => $next) {
                    if(empty($board[$l][$i+1])) {
                        
                    }
                }*/
            }
            
            /*if(($i < 14 && empty($board->boardgame[$l][$i+1]) || ($i > 0 && empty($board->boardgame[$l][$i-1])) {
                $board->crossCheck[$l][$c][$dir] = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
            }*/
        }
        
        return $crosscheck;
    }
    
    /**
     * Gets all anchors for the given board.
     * Anchors are empty boxes connected to letters on the board.
     * 
     * @param array $board
     * @return string[] Array of anchors indexes 
     */
    private function getAnchors(Array $board) {
        $anchors = array();
        
        // For each tile on the board, if the tile is empty and next to a ScrabbleLetter, then it's an anchor
        foreach ($board as $l => $line) {
            foreach ($line as $c => $box) {
                if(empty($box)) {
                    if(!empty($board[$l-1][$c])) $anchors[] = "$l,$c"; // Up
                    if(!empty($board[$l+1][$c])) $anchors[] = "$l,$c"; // Down
                    if(!empty($board[$l][$c-1])) $anchors[] = "$l,$c"; // Left
                    if(!empty($board[$l][$c+1])) $anchors[] = "$l,$c"; // Right
                }
            }
        }
        
        if(empty($anchors)) {
            $anchors[] = "7,7";
        }
        
        return $anchors;
    }
    
    /**
     * Transpose the given board
     * 
     * @param array $board The board game
     * @return array The transposed board
     */
    private function transposeBoard(Array $board) {
        $transBoard = array();
        
        foreach($board as $l => $line) {
            foreach ($line as $c => $box) {
                $transBoard[$c][$l] = $box;
            }
        }
        
        return $transBoard;
    }
}