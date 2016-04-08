<?php

$db = NULL;
try {
    $db = new PDO("mysql:host=localhost;dbname=ptiwiki", 'root', '');
    $db->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
    die($e->getMessage());
}