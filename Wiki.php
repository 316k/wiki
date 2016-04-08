<?php
/**
* Classe pour construire une page d'un Wiki
*  on transpose la structuration proposée par 
*   James Payne, Beginning Python, Wiley, 2010, p 430-431
*/
require_once 'Page.php';
class Wiki{
    private $base;
    
    function __construct($base){
        $this->base=$base;
    }
    
    function getBase(){
        return $this->base;
    }
    
    function getPage($nom){
        return new Page($this,$nom);
    }
}
?>
