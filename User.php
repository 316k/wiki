<?php

function logged_in() {

    if(isset($_SESSION['user_id'])) {
        return user($_SESSION['user_id']);
    }
    return FALSE;
}

// Trouve un utilisateur par (nom, password) ou par id
function user($id, $password = NULL) {
    global $db;

    if($password !== NULL) {
        $query = $db->prepare('SELECT * FROM users WHERE name = ? AND password = ?');
        $query->execute(array($id, md5($password)));
        return $query->fetch();
    }
    
    $query = $db->prepare('SELECT * FROM users WHERE id = ?');
    $query->execute(array(intval($id)));
    return $query->fetch();
}

function create_user($name, $password) {
    global $db;
    
    try {
        $query = $db->prepare('INSERT INTO users(name, password, rank) VALUES(?,?,"user")');
        $query->execute(array($name, md5($password)));
        return $db->lastInsertId();
    } catch(Exception $e) {
        return FALSE;
    }
}

function list_users() {
    global $db;
    
    $query = $db->query('SELECT * FROM users');

    return $query->fetchAll();
}