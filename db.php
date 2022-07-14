<?php

$hostname = 'localhost';
$database = 'dbs5827508';
$username = 'root';
$password = 'root';

try {
    $db = new PDO("mysql:host=$hostname;dbname=$database;charset=UTF8", $username, $password);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage() . "<br/>";
    die();
}
