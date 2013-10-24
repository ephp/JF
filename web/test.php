<?php
echo array() == array();
echo array('a') == array('a');
echo array('a', array('a' => 1)) == array('a', array('a' => 1));