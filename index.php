<?php
  require_once('include.php');
  require_once('config/config.php');

  // test connection mySQL
  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWD, DB_NAME);

  if ($mysqli->connect_error) {
        // problème connection mySQL =>STOP
        echo '<html><head><meta charset="utf-8"><title>MySQL Error</title><link rel="stylesheet" type="text/css" media="all"  href="css/mystyle.css" /></head><body>'.
             '<p>Impossible de se connecter à MySQL.</p>'.
             '<p>Voici le message d\'erreur : <b>'. utf8_encode($mysqli->connect_error) . '</b></p>'.
             '<br/>Vérifiez vos paramètres dans le config.php'.
             '</body></html>';        
  } else {
        // mySQL répond bien
        session_start();

        if(!isset($_SESSION["connected_user"]) || $_SESSION["connected_user"] == "") {
            // utilisateur non connecté
            header('Location: vw_login.php?notconnected');      

        } else {
            header('Location: vw_moncompte.php');      
        }
  }
?>

<html><head><meta charset="utf-8"><title>MySQL Error</title><link rel="stylesheet" type="text/css" media="all"  href="css/mystyle.css" /></head><body>