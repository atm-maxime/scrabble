<?php 

function sort_soutions($a, $b) {
    if($a->getScore() < $b->getScore()) return -1;
    if($a->getPosition() < $b->getPosition()) return -1;
    return 1;
}