<?php

class ScrabbleBoard
{
    private $boxes;
    private $boardgame;
    
    public function __construct($lang) {
        $conf = file('conf/'.$lang.'.board.conf');
        foreach ($conf as $line) {
            $data = explode(',', $line);
            $this->boxes[$data[0]] = trim($data[1]);
        }
        
        $this->boardgame = array();
        for ($i = 0; $i < 15; $i++) {
            $this->boardgame[$i] = array();
            for ($j = 0; $j < 15; $j++) {
                $this->boardgame[$i][$j] = '';
            }
        }
    }
    
    public function setLetter($tile, $idx) {
        list($x, $y) = $this->idx2i($idx);
        $this->boardgame[$x][$y] = $tile;
    }
    
    private function idx2i($idx) {
        $x = ord(substr($idx,0,1)) - 65;
        $y = (int)substr($idx,1,2) - 1;
        return array($x,$y);
    }
    
    private function i2idx($i, $j) {
        return chr(65 + $i) . $j+1;
    }
    
    public function printBoard() {
        print '<table class="board" cellspacing="0">';
        
        // First line with numeric index
        print '<tr class="">';
        print '<td class="idx">&nbsp;</td>';
        for ($i = 0; $i < 15; $i++) {
            $y = $i+1;
            print '<td class="idx">';
            print $y;
            print '</td>';
        }
        print '</tr>';
        
        for ($i = 0; $i < 15; $i++) {
            // First column with alphabetic index
            $x = chr(65 + $i);
            print '<tr class="boardline">';
            print '<td class="idx">';
            print $x;
            print '</td>';
            for ($j = 0; $j < 15; $j++) {
                $y = $j+1;
                $class = empty($this->boxes[$x.$y]) ? 'normal' : $this->boxes[$x.$y];
                $letter = empty($this->boardgame[$i][$j]) ? '&nbsp;' : $this->boardgame[$i][$j]->getTileHTML();
                print '<td class="letterbox '.$class.'" id="'.$x.$y.'">'.$letter.'</td>';
            }
            print '</tr>';
        }
        
        print '</table>';
    }
}

