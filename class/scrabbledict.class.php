<?php 
/*
 * Copyright (C) 2019       Maxime Kohlhaas     <maximekohlhaas@gmail.com>
 */

/**
 * Class to deal with a dictionary
 */
class ScrabbleDict {
    // Multidimensional array containing all words present in the loaded dictionnary
    public $dict;
    public $lang;
    
    function __construct($lang='fr') {
        // Load dictionary regarding the lang
        $this->lang = $lang;
        
        // Need to increase the memory to store the dictionary as an array
        // @TODO improve this by using something else thant an array...
        ini_set('memory_limit', '500M');
        //$this->dict = unserialize(file_get_contents('games/dict.game'));
    }
    
    /*
     * Creates an associative array based on a txt dictionary file
     * For now result is stored in a file as json data
     */
    function createDict($lang='fr') {
        global $data;
        $end = '.';
        echo $this->convert(memory_get_usage()).'<br>';
        // Load file containing all words for the lang
        $conf = file('conf/'.$lang.'.dictionary.conf', FILE_IGNORE_NEW_LINES);
        
        ////// IDEA OF AN ARRAY
        foreach ($conf as $j => $line) {
            $dict = &$this->dict;
            for ($i = 0; $i < strlen($line); $i++) {
                $letter = $line[$i];
                if($letter != "\n") {
                    if(!isset($dict[$letter])) $dict[$letter] = array();
                    $dict = &$dict[$letter];
                }
            }
            $dict['.'] = &$end;
            
            if($j > 10000) break;
        }
        
//         echo '<pre>';print_r($this->dict);
        echo $this->convert(memory_get_usage()).'<br>';
        file_put_contents('games/dict.game', serialize($this->dict));
        //echo '<pre>';print_r($this->dict);
        
        $this->tree2graph($this->dict, $data);
        echo $this->convert(memory_get_usage()).'<br>';
//         echo '<pre>';
//          print_r($data);
        unset($data);
        
        echo $this->convert(memory_get_usage()).'<br>';
        exit;
        
        ////// IDEA OF A GRAPH ?
        /*require 'Structures/Graph.php';
        $this->dict = new Structures_Graph(true);
        
        // For each word we will create an oriented connected graph
        foreach ($conf as $j => $line) {
            echo '<hr>'.$line.'<hr><pre>';
            $nArr = array();
            for ($i = 0; $i < strlen($line); $i++) {
                $letter = $line[$i];
                if($letter != "\n") {
                    $n = new Structures_Graph_Node();
                    $this->dict->addNode($n);

                    $n->setData($letter);
                    if(isset($nArr[count($nArr) - 1])) $nArr[count($nArr) - 1]->connectTo($n);
                    $nArr[] = $n;
                    var_dump($n);
                }
            }
            $n = new Structures_Graph_Node();
            $this->dict->addNode($n);
            $letter = '.';
            $n->setData($letter);
            $nArr[count($nArr) - 1]->connectTo($n);
            
            if(($j % 10000) == 0) echo $this->convert(memory_get_usage()).'<br />';
            if($j > 1) break;
            
        }
        
        //         echo '<pre>';print_r($this->dict);
        echo $this->convert(memory_get_usage());
        //         file_put_contents('games/dict.game', serialize($this->dict));
        exit;*/
    }
    
    function tree2graph(Array &$dict, &$data) {
//         global $data;
        foreach ($dict as $k => $sub) {
//             echo $k.'<hr>';
            if(is_array($sub)) $this->tree2graph($sub, $data);
            if($f = array_search($sub, $data)) {
                unset($dict[$k]);
                $dict[$k] = &$data[$f];
            }
            else $data[] = &$dict[$k];
        }
    }
    
    function convert($size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }
}

?>