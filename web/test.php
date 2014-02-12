<?php

    function currency($euro, $migliaia = '.', $decimali = ',') {
        return str_replace(array('€', $migliaia, $decimali, ' '), array('', '', '.', ''), $euro);
    }
    
    echo currency('1.000.000');