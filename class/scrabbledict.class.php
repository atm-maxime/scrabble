<?php 

class ScrabbleDict {
    // Multidimensional array containing all words present in the loaded dictionnary
    public $words;
    public $lang;
    
    function __construct($lang='fr') {
        // Load dictionary regarding the lang
        $this->lang = $lang;
        
        // Need to increase the memory to store the dictionary as an array
        // @TODO improve this by using something else thant an array...
        ini_set('memory_limit', '400M');
        $this->words = json_decode(file_get_contents('games/dict.game'));
    }
    
    /*
     * Creates an associative array based on a txt dictionary file
     * For now result is stored in a file as json data
     */
    function createDict($lang='fr') {
        // Load file containing all words for the lang
        $conf = file('conf/'.$lang.'.dictionary.conf', FILE_IGNORE_NEW_LINES);
        
        
        ////// IDEA OF AN ARRAY
        
        // For each word we will create an oriented connected graph
        foreach ($conf as $j => $line) {
            //if($j > 36000) echo '<hr>'.$line;
            $dict = &$this->words;
            for ($i = 0; $i < strlen($line); $i++) {
                $letter = $line[$i];
                if($letter != "\n") {
                    if(!isset($dict[$letter])) $dict[$letter] = array();
                    $dict = &$dict[$letter];
                }
            }
            $dict['.'] = '.';
            
            if(($j % 10000) == 0) echo $this->convert(memory_get_usage()).'<br />';
            if($j > 36850000) break;
            
        }
        
        echo '<pre>';echo json_encode($this->words);
        echo json_last_error().' - '.json_last_error_msg();
        echo $this->convert(memory_get_usage());
        file_put_contents('games/dict.game', json_encode($this->words));
        exit;
        
        ////// IDEA OF A GRAPH ?
        require 'Structures/Graph.php';
        $this->words = new Structures_Graph(true);
        
        // For each word we will create an oriented connected graph
        foreach ($conf as $j => $line) {
            echo '<hr>'.$line.'<hr><pre>';
            $nArr = array();
            for ($i = 0; $i < strlen($line); $i++) {
                $letter = $line[$i];
                if($letter != "\n") {
                    $n = new Structures_Graph_Node();
                    $this->words->addNode($n);

                    $n->setData($letter);
                    if(isset($nArr[count($nArr) - 1])) $nArr[count($nArr) - 1]->connectTo($n);
                    $nArr[] = $n;
                    var_dump($n);
                }
            }
            $n = new Structures_Graph_Node();
            $this->words->addNode($n);
            $letter = '.';
            $n->setData($letter);
            $nArr[count($nArr) - 1]->connectTo($n);
            
            if(($j % 10000) == 0) echo $this->convert(memory_get_usage()).'<br />';
            if($j > 1) break;
            
        }
        
        //         echo '<pre>';print_r($this->words);
        echo $this->convert(memory_get_usage());
        //         file_put_contents('games/dict.game', serialize($this->words));
        exit;
    }
    
    function convert($size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }
}

?>