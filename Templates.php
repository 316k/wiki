<?php
require_once 'MarkDown.php';

// templates transposés de 
// James Payne, Beginning Python, Wiley, 2010, p 435-436

function mainTPL($title,$body,$navlinks, $usermenu){
    $usermenu = viewMenuTPL();
    return <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
    	<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
        <title>$title</title>
        <link rel="stylesheet" href="assets/css/style.css" />
    </head>
    <body>
        $body
        <hr></hr>
        $navlinks $usermenu
    </body>
</html>
HTML;
}

function viewTPL($banner,$processedText){
    return <<<VIEW
    $banner
    $processedText
VIEW;
}

function editTPL($banner,$pageURL,$text){
    return <<<WRITE
    $banner
    <form method="POST" action="PtiWiki.php">
        <input type="hidden" name="op" value="save"></input>
        <input type="hidden" name="file" value="$pageURL"></input>
        <textarea rows="15" cols="80" name="data">$text</textarea>
        <br></br>
        <input type="submit" value="sauver"></input>
    </form>
WRITE;
}

function errorTPL($error){
    return <<<HTML
    <h1>Erreur: $error</h1>
    <p>Vous pouvez toujours essayer de retourner à l'<a href="PtiWiki.php?op=read&file=PageAccueil">Accueil</a> ou de vous <a href="PtiWiki.php?op=login">logguer</a>.</p>
HTML;
}

function bannerTPL($banner){
    return "<p style='color:green'>$banner</p><hr></hr>";
}

function viewLinkTPL($file,$name){
    global $wiki;
    if(file_exists("{$wiki->getBase()}/$file.md")){
        $op="read";
        $style="";
    } else { // new file, make the link in red and set op to create
        $op="create";
        $style=" style='color:red'";
    }
    return "<a href='PtiWiki.php?op=$op&file=$file'$style>$name</a>";
}

function viewMenuTPL() {
    $user = logged_in();
    if(!$user)
        return "";
    
    $links = array(
        '<strong>' . $user['name'] . '</strong>',
        '<a href="PtiWiki.php?op=logout">Logout</a>'
    );
    
    if($user['rank'] == 'admin')
        $links[] = '<a href="PtiWiki.php?op=admin">Administration</a>';

    return '<div class="user-menu">' . implode(' | ', $links) . '</div>';
}

function editLinkTPL($file,$name){
    return "<a href='PtiWiki.php?op=update&file=$file'>$name</a>";
}

function deleteLinkTPL($file,$name){
    return "<a href='PtiWiki.php?op=delete&file=$file'>$name</a>";
}

function deleteTPL($pageURL){
    return <<<DELETE
    <p>Êtes-vous certain de vouloir détruire la page "$pageURL"</p>
    <form method="GET action="PtiWiki.php">
        <input type="hidden" name="op" value="confirm-delete"></input>
        <input type="hidden" name="file" value="$pageURL"></input>
        <input type="submit" value="Détruire $pageURL!"></input>
    </form>    
DELETE;
}

?>
