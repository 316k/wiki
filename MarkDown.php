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
    // montrer tout le HTML...
    $texte = preg_replace("/</","&lt;",$texte);
    // remplacer les **xxx** par  <b>xxx</b>
    $texte = preg_replace("/\*\*(.*?)\*\*/","<b>$1</b>",$texte);
    // remplacer les *xxx*   par  <em>xxx</em>
    $texte = preg_replace("/\*(.*?)\*/","<em>$1</em>",$texte);
    // remplacer les ![alt](src)   par  <img src="src" alt="alt" />
    $texte = preg_replace("/!\[(.*?)\]\((.*?)\)/","<img src='$2' alt='$1' />",$texte);
    // remplacer une série de deux lignes ou plus débutant par un tiret par une liste non numéroté
    $nb = preg_match_all("/((\r?\n-)(.*)){2,}/",$texte,$listes);
    for($i=0;$i<$nb;$i++){
     $lis = preg_replace("/(\r?\n)- *(.*)/","<li>$2</li>",$listes[0][$i]);
     $texte = str_replace($listes[0][$i],"<ul>$lis\n</ul>",$texte);
    }
    // remplacer les # par des titres
    $re_titre = "/^(#+) *(.*)/m";
    $nb = preg_match_all($re_titre,$texte,$titres);
    for($i=0;$i<$nb;$i++){
     $niveau = strlen(preg_replace($re_titre,"$1",$titres[0][$i]));
     $titre = preg_replace($re_titre,"$2",$titres[0][$i]);
     $texte = str_replace($titres[0][$i],"\n<h$niveau>$titre</h$niveau>",$texte);
    }
    // remplacer les références de la forme [lien](url) par <a href="url">lien</a>
    $texte = preg_replace("/\[(.*?)\]\((.*?)\)/","<a href='$2'>$1</a>",$texte);
    // remplacer les WikiWord par <a href="PtiWiki.php?op='view'&file='WikiWord'>WikiWord</a>"
    $texte = preg_replace_callback("/(\b[A-Z]\w*?[A-Z]\w*?\b)/",
                                   "viewLinkCallback",
                                    $texte);
    // remplacer les "double newline" par </p><p>
    $texte = preg_replace("/(\r?\n){2}/","</p>\n<p>",$texte); 
    // entourer le tout de <p></p>
    return "<p>".$texte."</p>";
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