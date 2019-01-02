<?php 

function sort_soutions_by_points($a, $b) {
    if($a->getPoints() < $b->getPoints()) return 1;
    return -1;
}

function sort_soutions_by_position($a, $b) {
    if($a->getPoints() == $b->getPoints()) {
        if($a->getPosition() < $b->getPosition()) return 1;
        return -1;
    }
    return 0;
}