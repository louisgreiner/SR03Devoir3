<?php
  require_once('include.php');
  session_set_cookie_params(5,"/");
  session_start();

    if(!(isset($_SESSION['connected_user']))){
      // pas de session active = d'utilisateur déjà connecté
      header('Location: vw_login.php');
    }

    $mytoken = bin2hex(random_bytes(128)); // token qui va servir à prévenir des attaques CSRF 
    $_SESSION["mytoken"] = $mytoken;

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
  else
  {
      header("Location:vw_login.php?notconnected");
  }

?>

<!DOCTYPE html>
<html>
  <meta charset="UTF-8">
    <head>
      <!-- Polices d'écriture et style CSS -->
      <link rel="stylesheet" href="css/newstyle.css">
      <link href="https://fonts.googleapis.com/css?family=Alegreya&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css?family=Crimson+Text&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css?family=Lora&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css?family=Nobile&display=swap" rel="stylesheet">
      <link rel="icon" href="images/favicon.png">
      <link rel="stylesheet" href="fonts/fonts.css" type="text/css"  />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      <title>La banque du futur</title>
    </head>
    <body>
    
    <!-- Barre de navigation -->
    <div class="newnav">
      <div style="background-color:white;height:80px">
          <a href="vw_moncompte.php"><img class="logo" height="60px" style="margin-left:95px;margin-top:-8px;" src="images/logoRoro.png" alt="logo"></a>
          <div style="margin-left:3.5%" class="dropdown"><a href="vw_envoyermessage.php"><button class="dropbtn">Message</button></a></div>
          <div class="dropdown"><a href="vw_virement.php"><button class="dropbtn">Virement</button></a></div>
          <div class="dropdown">
          <form action="myController.php">
            <input name="action" type="hidden" value="msglist">
            <input type="submit" id="dropbtn" value="Messagerie">
          </form>
          </div>
          <form action="myController.php" method="POST">
            <input type="hidden" name="action" value="disconnect">
            <input type="hidden" name="loginPage" value="vw_login.php?disconnect">
            <button class="decobutton">Déconnexion</button>
          </form>
         <br>
      </div>
      <!-- Ombre sous la barre -->
      <div class="shadow"></div>
    </div><br><br>

    <?php
      $style = "";
      if (isset($_REQUEST["bad_mt"])) {
          echo '
            <div class="formulaire" style="background-color:#ff7770;padding:10px;padding-top:20px;padding-bottom:20px;text-align:center;color:white;font-size:1.25rem;font-weight:bold;font-family:'."Roboto".';">
              Montant incorrect...

            </div>';
          $style = "margin: 2% 35% 0%;";
        } else if (isset($_REQUEST["bad_dest"])) {
          echo '
            <div class="formulaire" style="background-color:#ff7770;padding:10px;padding-top:20px;padding-bottom:20px;text-align:center;color:white;font-size:1.25rem;font-weight:bold;font-family:'."Roboto".';">
              Destinataire incorrect... 

            </div>';
          $style = "margin: 2% 35% 0%;";
        } else if (isset($_REQUEST["max_mt"])) {
          echo '
            <div class="formulaire" style="background-color:#ff7770;padding:10px;padding-top:20px;padding-bottom:20px;text-align:center;color:white;font-size:1.25rem;font-weight:bold;font-family:'."Roboto".';">
              Contactez votre conseiller pour les virements > 10000€

            </div>';
          $style = "margin: 2% 35% 0%;";
        } else if(isset($_REQUEST["trf_ok"])){
          echo '
            <div class="formulaire" style="background-color:#7ac27e;padding:10px;padding-top:20px;padding-bottom:20px;text-align:center;color:white;font-size:1.25rem;font-weight:bold;font-family:'."Roboto".';">
              Virement effectué avec succès '.$_REQUEST["trf_ok"].'

            </div>';
          $style = "margin: 2% 35% 150px;";
        } else if(isset($_REQUEST["mpd_incorrect"])){
          echo '
            <div class="formulaire" style="background-color:#ff7770;padding:10px;padding-top:20px;padding-bottom:20px;text-align:center;color:white;font-size:1.25rem;font-weight:bold;font-family:'."Roboto".';">
              Mot de Passe Incorrect

            </div>';
          $style = "margin: 2% 35% 0%;";
        }
      ?>
      <div class="formulaire" style="height:730px;<?php echo ("$style");?>padding:10px;margin-bottom:100px;border-width:2px;">
          <div class="inscription" style="  margin-top:1%">
            <img height="70px" style="padding:0px;margin-left:3.25%" src="images/send_money.png" alt="icon">
            <h3 style="font-size:3.5rem;text-align:center;color: #333"> Virement</h3>
          </div>
        <form style=" font-family: 'Roboto', sans-serif;font-size:1.5rem;margin-left:60px; margin-right:60px;" class="" action="myController.php" method="post">

           <input type="hidden" name="action" value="transfert">
           <input type="hidden" name="mytoken" value="<?php echo $mytoken; ?>">
           <center>
           <label for="login">Votre solde : <?php echo number_format($_SESSION["connected_user"]["solde_compte"], 2, ',', ' ')?>€</label><br><br><br>
           </center>

           <label for="login">Compte destinataire</label><br>

                <select name="destination" class="custom-select" style="width:95%;text-decoration:none;background-color:white;border:none;" required>
                    <?php
                        foreach ($_SESSION['listeUsers'] as $id => $user) {
                            if($_SESSION["connected_user"]["id_user"] != $id){
                                if (isset($_REQUEST['id']) && $id == $_REQUEST['id']) {
                                  echo '<option value="'.$user['numero_compte'].'" "'.'style="display:none;margin-top:10px;" selected>'.$user['nom'].' '.$user['prenom'].' (n° '.$user['numero_compte'].')</option>';
                                }
                                else{
                                    echo '<option value="'.$user['numero_compte'].'" "'.'style="display:none;margin-top:10px;"'.'">'.$user['nom'].' '.$user['prenom'].' (n° '.$user['numero_compte'].')</option>';
                                }
                            }
                        }
                    ?>          
                </select>
                <hr class="underrule">

            <label for="login">Montant à transférer</label><br>
                <input type="number" class="input_euros" name="montant-euros" placeholder="135" min="0" max="<?php echo $_SESSION["connected_user"]["solde_compte"]?>" required><span class="euros">€</span>
                <input type="number" class="input_centimes" placeholder="47" name="montant-centimes" max="99" min="0" required><br>
                <hr class="undereuros">
                <hr class="undercentimes">
            <label for="prenom">Mot de passe</label><br>
            <input type="password" name="mdp" required minlength="1" placeholder="********"><br>
            <hr class="underrule"><br>
            <center>
            <a id="homebutton" href="vw_moncompte.php">Accueil<a>
            <span class="espace"></span>
            <input type="submit">
            </center>

        </form>
      

  </body>
</html>
