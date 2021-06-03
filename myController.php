<?php
  require_once('myModel.php');
  require_once('include.php');
  session_start();

  // Vérification que la session n'a pas expiré
  if(isset($_SESSION["connected_user"]["id_user"]))
  {
      if(time()-$_SESSION["login_time"] > 600) // drop la session au bout de 10min = 600 sec
      {
          session_unset();
          session_destroy();
          header("Location:vw_login.php?timeout");
      }
  }

  
  // URL de redirection par défaut (si pas d'action ou action non reconnue)
  $url_redirect = "index.php";
  
  if (isset($_REQUEST['action'])) {

      if ($_REQUEST['action'] == 'authenticate') {

          /* ======== AUTHENTICATE ======== */
          
          if (isset($_POST['g-recaptcha-response']) && !($_POST['g-recaptcha-response'])){
            $url_redirect = "vw_login.php?misscaptcha";
          } else {
            $captcha = $_POST['g-recaptcha-response'];
            $secretKey = "6Lfb-C4aAAAAADizThVKjv32pzOyNYcjBuidEY15";
            $ip = $_SERVER['REMOTE_ADDR'];
            $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($captcha);
            $response = file_get_contents($url);
            $responseKeys = json_decode($response,true);
            if(!($responseKeys["success"])) {
                $url_redirect = "vw_login.php?badcaptcha";
            } else if (ipIsBanned($_SERVER['REMOTE_ADDR'])){
                // cette IP est bloquée
                $url_redirect = "vw_login.php?ipbanned";
            } else if (!isset($_REQUEST['login']) || !isset($_REQUEST['mdp']) || $_REQUEST['login'] == "" || $_REQUEST['mdp'] == "") {
                // manque login ou mot de passe
                $url_redirect = "vw_login.php?nullvalue";
                
            } else {
            
                $utilisateur = findUserByLoginPwd($_REQUEST['login'], $_REQUEST['mdp'],$ip);
                
                if ($utilisateur == false) {
                    // echec authentification
                    $url_redirect = "vw_login.php?badvalue";
                    
                } else {
                    // authentification réussie
                    $_SESSION["connected_user"] = $utilisateur;
                    $_SESSION["listeUsers"] = findAllUsers();
                    // initialisation de l'horaire de début de session
                    $_SESSION["login_time"] = time();
                    $url_redirect = "vw_moncompte.php";
                }
            }
        }
         
          
      } else if ($_REQUEST['action'] == 'disconnect') {
          /* ======== DISCONNECT ======== */
          unset($_SESSION["connected_user"]);
          $url_redirect = "vw_login.php?disconnect";
          
      } else if ($_REQUEST['action'] == 'transfert') {
          /* ======== TRANSFERT ======== */
            $eur = $_REQUEST['montant-euros'];
            $cent = $_REQUEST['montant-centimes'];
            $mdp = $_REQUEST['mdp'];
            $total = $eur.'.'.$cent; 
            $utilisateur = findUserByNumCompte($_REQUEST["destination"]);
            if (!isset($_REQUEST['mytoken']) || $_REQUEST['mytoken'] != $_SESSION['mytoken']) {
              // echec vérification du token (ex : attaque CSRF)
              $url_redirect = "vw_moncompte.php?err_token";
            } else if ($utilisateur == false || $utilisateur["numero_compte"] == $_SESSION["connected_user"]["numero_compte"]){
                $url_redirect = "vw_virement.php?bad_dest";
            } else if (!findUserByLoginPwd($_SESSION["connected_user"]['login'], $mdp, $_SERVER['REMOTE_ADDR'])){
                $url_redirect = "vw_virement.php?mpd_incorrect";
            } else {
                if (!(is_numeric($eur)) || !(is_numeric($cent)) || !(is_numeric($total)) || intval($cent) < 0 || intval($cent) > 99 || floatval($total) > $_SESSION["connected_user"]["solde_compte"] || floatval($total) <= 0) {
                    $url_redirect = "vw_virement.php?bad_mt";
                } else if (floatval($total) > 10000) {
                    $url_redirect = "vw_virement.php?max_mt";
                } else if (compteIsBanned($_SESSION["connected_user"]["numero_compte"], floatval($total))){
                    $url_redirect = "vw_virement.php?max_vir";
                } else {
                    transfert($_REQUEST['destination'],$_SESSION["connected_user"]["numero_compte"], floatval($total));
                    $_SESSION["connected_user"]["solde_compte"] = getSolde($_SESSION["connected_user"]["id_user"]);
                    $url_redirect = "vw_virement.php?trf_ok";
                }
            }
      } else if ($_REQUEST['action'] == 'sendmsg') {

          /* ======== MESSAGE ======== */
          $utilisateur = findUserByID($_REQUEST['to']);
            if (  $utilisateur == false || $utilisateur["id_user"] == $_SESSION["connected_user"]["id_user"]){
                $url_redirect = "vw_virement.php?bad_dest";
            }

          addMessage($_REQUEST['to'],$_SESSION["connected_user"]["id_user"],$_REQUEST['sujet'],$_REQUEST['corps']);
          $url_redirect = "vw_envoyermessage.php?msg_ok";
              
      } else if ($_REQUEST['action'] == 'msglist') {

          /* ======== MESSAGE ======== */
            $_SESSION['messagesRecus'] = findMessagesInbox($_SESSION["connected_user"]["id_user"]);
            $url_redirect = "vw_messagerie.php";              
      } 

       
  }  
  
  header("Location: $url_redirect");

?>
