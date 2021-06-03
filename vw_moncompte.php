<?php
  require_once('include.php');
  session_set_cookie_params(5,"/");
  session_start();

    if(!(isset($_SESSION['connected_user']))){
      // pas de session active = d'utilisateur déjà connecté
      header('Location: vw_login.php');
    }

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
      <!-- <link rel="stylesheet" href="fonts/fonts.css" type="text/css"  /> -->
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


    <h3 style="font-size:3.5rem;text-align:left;color: #333;padding-top: 4%;position:absolute;padding-left: 5%;font-family: 'boldfont', serif;font-weight: lighter;">
      Bienvenue 
      <?php 
        echo $_SESSION['connected_user']['prenom']." ".$_SESSION['connected_user']['nom']; 
      ?> 
    </h3>
    <br><br><br><br><br><br><br><br><br><br><br>
    <h2 style="margin-left: 7%;font-family: 'Roboto', sans-serif;">Utilisez les menus deroulants ou un des raccourcis rapides : </h2><br><br>

    <div style= "width: 100%; height: 200px; margin: auto; padding: 10px;">
      
      <!-- Widget raccourcis rapides -->
      <div style="margin-left: 12%;width:430px;float:left;height:230px;border:3px solid black;border-radius:8px;">
      <center>
        <h2 style="padding-left:12px;padding-right:12px;font-family: 'Roboto', sans-serif;width:130px;margin-top:-20px;background:white;">Raccourcis</h2>
        <form action="vw_envoyermessage.php">
          <button class="withinframe">Envoyer un message</button>    
        </form> 
        <form action="vw_virement.php">
          <button class="withinframe">Effectuer un virement</button>    
        </form> 
        <form action="myController.php">
          <input name="action" type="hidden" value="msglist">
          <button class="withinframe">Accès à la messagerie</button>    
        </form>    
        </center>
      </div>

      <!-- Widget infos du compte -->
      <div style="height:230px;float:right;width:430px;margin-right:14%;border:3px solid black;border-radius:8px;">
       <center>
        <h2 style="padding-left:12px;padding-right:12px;font-family: 'Roboto', sans-serif;width:200px;margin-top:-20px;background:white;">Vos informations</h2>
        <br>
        <h2 style="margin-top:-2%;font-size:18px;font-family: 'Roboto', sans-serif;">Numéro de compte : <?php echo $_SESSION['connected_user']['numero_compte']; ?></h2><br><br>
        <h2 style="margin-top:-7%;font-size:18px;font-family: 'Roboto', sans-serif;"><?php echo $_SESSION['connected_user']['nom'].' '.$_SESSION['connected_user']['prenom']; ?></h2><br><br>
        <h2 style="margin-top:-7%;font-size:18px;font-family: 'Roboto', sans-serif;">Solde : <?php echo number_format($_SESSION["connected_user"]["solde_compte"], 2, ',', ' '); ?>€</h2><br><br>
        <h2 style="margin-top:-7%;font-size:18px;font-family: 'Roboto', sans-serif;">Profil : <?php echo $_SESSION['connected_user']['profil_user']; ?></h2><br><br>

        </center>

      </div>
    </div>
    <br><br><br><br><br><br>
    <?php 
    if($_SESSION["connected_user"]["profil_user"] == "EMPLOYE"){
    ?>
      <center>
        <a class="deco" href="vw_ficheclient.php" style="font-size:22px;padding:16px;">Consulter les clients<img src="images/loupe2.png" style="margin-left: 10px;" height="25px" alt=""></a>
      </center>
    <?php
    }
    ?>
    </div>
  </body>
</html>
