<?php

$hostname = 'db5007059843.hosting-data.io:3306';
$database = 'dbs5827508';
$username = 'dbu1157779';
$password = '!8c9A!X5j';

try{
    $db = new PDO("mysql:host=$hostname;dbname=$database;charset=UTF8", $username, $password);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage() . "<br/>";
    die();
}