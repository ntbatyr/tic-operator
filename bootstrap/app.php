<?php
echo __DIR__;
die();

 require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
 require __DIR__ . 'helpers.php';

 use Models\Database;
 new Database();
