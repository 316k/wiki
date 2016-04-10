<?php
/**
 * Création en PHP d'un système simple de Wiki
 *  inspiré très fortement par la structuration proposée dans  
 *        James Payne, Beginning Python, Wiley, 2010, p 430-431
 *  Les pages sont conservées dans un répertoire ouvert en écriture pour tous...
 *  Il serait préférable  d'utiliser une BD avec une meilleure gestion des usagers.
 */

session_start();
ob_start();

require_once 'Db.php';
require_once 'User.php';
require_once 'Wiki.php';
require_once 'Templates.php';

//  analyser les paramètres d'entrée
$method = $_SERVER['REQUEST_METHOD'];
if($method=='POST' && isset($_POST['op']) && isset($_POST['file'])) {
    $op = $_POST["op"];
    $file = $_POST["file"];
} else {
	if(array_key_exists("op",$_GET)) $op = $_GET["op"];
    else $op="view";
	if(array_key_exists("file",$_GET))$file = $_GET["file"];
	else $file="PageAccueil";
}

	
$wiki = new Wiki("Wk");
$title = "PtiWiki - $file";

$page = $wiki->getPage("$file.md");

if($page->exists())
    $page->load();
else if(!in_array($op, array('create', 'save')))
    $op = 'not_found';

$navlinks = array(viewLinkTPL("PageAccueil","Accueil"),
                  editLinkTPL($file,"Éditer"));

if($file!="PageAccueil")
    $navlinks[] = deleteLinkTPL($file,"Détruire");

$navlinks = '<div>' . implode(' | ', $navlinks) . '</div>';

if(!logged_in() && in_array($op, array('create', 'update', 'delete', 'confirm-delete', 'save'))) {
    $op = 'unauthorized';
}


switch ($op) {
    case 'create':
        echo mainTPL($title,editTPL(bannerTPL("Création de $file"),
                                   $file,""),
                     $navlinks);
        break;
    case 'read':
        echo mainTPL($title,viewTPL(bannerTPL($title),
                                    markDown2HTML($page->getText())),
                    $navlinks);
        break;
    case 'update':
        echo mainTPL($title,editTPL(bannerTPL($title),
                                    $file,$page->getText()),
                     $navlinks);
        break;
    case 'delete':
        echo mainTPL($title,deleteTPL($file),$navlinks);
        break;
    case 'confirm-delete':
        $page->delete();
        echo mainTPL($title,viewTPL("PtiWiki - [Page $file détruite!]",
                                     markDown2HTML($wiki->getPage("PageAccueil.md")->load()->getText())),
                     $navlinks);
        break;
    case 'save':
        // truc adapté de http://www.tizag.com/phpT/php-magic-quotes.php
		if(get_magic_quotes_gpc())
			$newText = stripslashes($_POST['data']);
		else
			$newText = $_POST['data'];
        $page->setText($newText)->save();
        echo mainTPL($title,viewTPL(bannerTPL($title),
                                    markDown2HTML($newText)),
                     $navlinks);
        break;
    case 'signup':
        if(isset($_POST['name']) && isset($_POST['password'])) {
            $_SESSION['user_id'] = create_user($_POST['name'], $_POST['password']);
            header('Location: PtiWiki.php?op=read&file=PageAccueil');
        }
        echo signupTPL("Signup");
        break;
    case 'login':
        if(isset($_POST['name']) && isset($_POST['password'])) {
            $user = user($_POST['name'], $_POST['password']);
            if($user)
                $_SESSION['user_id'] = $user['id'];
            header('Location: PtiWiki.php?op=read&file=PageAccueil');
        }
        echo loginTPL(bannerTPL("Login"));
        break;
    case 'logout':
        session_unset();
        header('Location: PtiWiki.php?op=read&file=PageAccueil');
        exit();
        break;
    case 'admin':
        echo mainTPL("TODO", "TODO", "");
        // Voir le log & modifier des users
        break;
    case 'unauthorized':
        echo mainTPL("Erreur", errorTPL("Vous devez d'abord vous connecter"), "");
    break;
    default:
        echo mainTPL("Erreur", errorTPL("Page introuvable"), "");
        break;
}
?>
