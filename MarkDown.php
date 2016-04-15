<?php

// création de tags HTML
function tag($tag,$body,$attrs=""){
	$out = "<$tag";
	if($attrs)$out+=" $attrs";
    return "$out>$body</$tag>";
}

// transformer le simili MarkDown en HTML
function markDown2HTML($texte){
    global $w;
    // remplacer les WikiWord par <a href="PtiWiki.php?op='view'&file='WikiWord'>WikiWord</a>"
    $texte = preg_replace_callback("/(\b[A-Z]\w*?[A-Z]\w*?\b)/",
                                   "viewLinkCallback",
                                    $texte);
    return $texte;
}

// call back utilisé pour la génération des liens vers les pages Wiki indentifiées par les WikiWords
function viewLinkCallback($matches){
    return viewLinkTPL($matches[1],$matches[1]);
}

// fonction pour tester isolément la transformation markDown2HTML
//  attention la transformation de WikiWords ne fonctionne pas isolément...

function markDown2HTMLTest(){
	$body = <<<BODY
# Le grand titre
## Un sous-titre
et voici du *texte en italique* et en **gras** et une liste 
- item1
- item2

Un nouveau paragraphe

Et voici [un lien](http://www.iro.umontreal.ca) qui devrait aller au Diro
et une deuxième liste
- item3
- item4

Et du html tel quel:
<html>du texte</html>
BODY;

	echo <<<START
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
START;
	$head = tag("head",
				tag("meta","",'http-equiv="content-type" content="text/html;charset=utf-8"').
				tag("title","une page de test"));
	echo tag("html",
	         "\n".$head.
	         tag("body",markDown2HTML($body)),
	         "xmlns='http://www.w3.org/1999/xhtml'");
}

// appel du test unitaire
// markDown2HTMLTest();

?>
