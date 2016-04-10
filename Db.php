<?php

require_once "config.php";

$db = NULL;
try {
    $db = new PDO("mysql:host=localhost;dbname=" . db_name, db_user, db_password);
    $db->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
}
catch(PDOException $e) {
    die($e->getMessage());
}