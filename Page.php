<?php
/**
* Classe pour construire une page d'un Wiki
*  on transpose la structuration proposée par 
*   James Payne, Beginning Python, Wiley, 2010, p 430-431
*/
class Page{
    private $wiki, $nom, $nomFichier, $texte;

    public static function index($filter) {

        $list = array();
        
        foreach(scandir('Wk') as $file) {
            $name = preg_replace('/(.*)\.md/', '$1', $file);

            if(!in_array($name, array('.', '..')) && ($filter == "" || stripos($name, $filter) !== FALSE))
                $list[] = $name;
        }
        
        return $list;
    }
    
    function __construct($wiki,$nom){
        $this->wiki=$wiki;
        $this->nom=$nom;
        $this->nomFichier=$wiki->getBase()."/".$nom;
    }
    
    function exists(){
        return file_exists($this->nomFichier);
    }
    
    function load(){
        $handle = fopen($this->nomFichier, "r");
        if(!$handle) die("erreur d'ouverture de {$this->nomFichier}");
        $this->texte = fread($handle, filesize($this->nomFichier));
        fclose($handle);
        return $this;
    }
    
    function save(){
        $handle = fopen($this->nomFichier,"w");
        if(!$handle) die("erreur d'ouverture de {$this->nomFichier}");
        fwrite($handle,$this->texte);
        fclose($handle);
        return $this;
    }
    
    function delete(){
        unlink($this->nomFichier);
    }
    
    function getText(){
        return $this->texte;
    }
    
    function setText($texte){
        $this->texte=$texte;
        return $this;
    }
    
}

?>