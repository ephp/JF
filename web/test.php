<?php
try {
    $a = null;
    echo $a->__toString();
} catch (\Error $e) {
    echo 'JF'.substr(strrev(uniqid()),0,8);
}