<?php
var_dump($_FILES);
$path = $_SERVER['DOCUMENT_ROOT'].'/test.txt';
var_dump($path);
var_dump(file_exists($path));
file_put_contents($path, var_export($_FILES,true));
?>