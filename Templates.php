<?php
require_once 'MarkDown.php';

// templates transposés de 
// James Payne, Beginning Python, Wiley, 2010, p 435-436

function mainTPL($title,$body,$navlinks){
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
        <hr />
        <div class="footer">
            $navlinks $usermenu
            <br style="clear: both" />
        </div>
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

function loginTPL($banner) {
    return <<<WRITE
    $banner
    <div class="formdiv">
        <h1 class="login_title">Veuillez entrez vos information pour vous connecter</h1>
        <form method="POST" action="">
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" id="username" name="name" placeholder="user" />
            <br/>
            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" placeholder="password" />
            <br/>
            <input type="submit" value="Login"></input>
        </form>
    </div>
WRITE;
}

function signupTPL($banner) {
    return <<<WRITE
    $banner
    <div class="formdiv">
        <h1 class="login_title">Veuillez entrez un nom d'utilisateur et un mot de passe pour vous enregistrer</h1>
        <form method="POST" action="">
            <label for="name">Nom d'utilisateur:</label>
            <input type="text" id="name" name="name" placeholder="user" />
            <br/>
            <label for="pass">Mot de passe:</label>
            <input type="password" id="pass" name="password" placeholder="password" />
            <br/>
            <input type="submit" value="Login"></input>
        </form>
    </div>
WRITE;
}

function indexTPL($banner, $filter) {
    $index = $banner . <<<INDEX
    <h2>Index des pages</h2>
    <form method="GET" action="">
        <input type="hidden" name="op" value="index" />
        <input type="text" name="filter" value="$filter" placeholder="ex.: OuLiPo" />
        <input type="submit" value="filtrer" />
    </form>
    <ul>
INDEX;
    
    foreach(Page::index($filter) as $page) {
        $index .= '<li>' . viewLinkTPL($page, $page) . '</li>';
    }
    
    return $index . "</ul>";
}

function adminTPL($banner, $users, $logs) {
    $out = <<<HTML
    
    $banner
    <h2>Utilisateurs</h2>
    <table>
        <thead>
            <tr>
                <td></td>
                <th>Rang</th>
                <th>Contribution</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
HTML;

    foreach($users as $user) {
        $ban_word = $user['rank'] == 'banned' ? 'Débannir' : 'Bannir';
        $ban_value =  $user['rank'] == 'banned' ? 'user' : 'banned';
        $admin_word = $user['rank'] == 'admin' ? 'Enlever les pouvoirs de modérations' : 'Donner les pouvoirs de modération';
        $admin_value = $user['rank'] == 'admin' ? 'user' : 'admin';
        $out .= '<tr><th>' . $user['name'] . '</th>';
        $out .= '<td>' . $user['rank'] . '</td>';
        $out .= '<td style="text-align: right">' . user_contribution($user['id']) . '%</td>';
        $out .= '<td>';
        if ($user['rank'] != 'admin'){
            $out .= '<a href="PtiWiki.php?op=admin&amp;rank&#61;' . $ban_value . '&amp;user=' . $user['name'] . '">' . $ban_word . '</a> ';
        }
        if($user != logged_in() && $user['rank'] != "banned") {
            $out .= '<a href="PtiWiki.php?op=admin&amp;rank&#61;' . $admin_value . '&amp;user=' . $user['name'] . '">' . $admin_word . '</a>';
        }

        $out .= '</td></tr>';
    }

    $out .= <<<HTML
        </tbody>
    </table>
    <h2>Log</h2>
    <table>
        <thead>
            <tr>
                <td></td>
                <th>Utilisateur</th>
                <th>Action</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
HTML;

    foreach($logs as $log) {
        $out .= '<tr><th>' . viewLinkTPL($log['page'],$log['page']) . '</th>';
        $out .= '<td>' . user($log['user_id'])['name'] . '</td>';
        $out .= '<td>' . $log['action'] . '</td>';
        $out .= '<td>' . $log['date'] . '</td></tr>';
    }

    $out .= '</tbody></table>';
    
    return $out;
}

function errorTPL($error){
    return <<<HTML
    <h1>Erreur: $error</h1>
    <p>Vous pouvez toujours essayer de retourner à l'<a href="PtiWiki.php?op=read&amp;file=PageAccueil">Accueil</a> ou de vous <a href="PtiWiki.php?op=login">logguer</a>.</p>
HTML;
}

function bannedTPL($message) {
    return <<<HTML
    <h1>Oh non !</h1>
    <p>$message</p>
HTML;
}

function bannerTPL($banner){
    return "<p style='color:green'>$banner</p><hr />";
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
    return "<a href='PtiWiki.php?op=$op&amp;file=$file'$style>$name</a>";
}

function viewMenuTPL() {
    $user = logged_in();
    if(!$user) {
        $links = array(
            '<em><a href="PtiWiki.php?op=signup">Signup</a></em>',
            '<em><a href="PtiWiki.php?op=login">Login</a></em>',
        );
    } else {
        $links = array(
            '<strong>' . $user['name'] . '</strong>',
            '<a href="PtiWiki.php?op=logout">Logout</a>'
        );
        
        if($user['rank'] == 'admin')
            $links[] = '<a href="PtiWiki.php?op=admin">Administration</a>';
    }
    
    return '<div class="user-menu">' . implode(' | ', $links) . '</div>';
}

function editLinkTPL($file,$name){
    return "<a href='PtiWiki.php?op=update&amp;file=$file'>$name</a>";
}

function deleteLinkTPL($file,$name){
    return "<a href='PtiWiki.php?op=delete&amp;file=$file'>$name</a>";
}

function viewSourceLinkTPL($file,$name){
    return "<a href='PtiWiki.php?op=view_source&amp;file=$file'>$name</a>";
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
