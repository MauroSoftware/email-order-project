<?php

if (!class_exists('MysqliDb')) {
    require_once '../class/MysqliDb.php';
}

$db = new MysqliDb('localhost', 'root', '', 'test_db'); // inserire qui le credenziali di accesso al database
